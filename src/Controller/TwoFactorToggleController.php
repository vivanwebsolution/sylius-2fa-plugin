<?php

namespace VivanWebSolution\Sylius2FAPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class TwoFactorToggleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepositoryInterface $adminUserRepository,
        private GoogleAuthenticatorInterface $googleAuthenticator
    ) {}

    /**
     * 1. Toggle 2FA on/off for a user via AJAX.
     *    - When enabling, generate secret if none exists.
     *    - When disabling, clear secret and disable flag.
     *    - After enabling, redirect to QR setup page.
     */
    public function toggle2fa(Request $request): JsonResponse
    {
        $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->isCsrfTokenValid('toggle_2fa', $csrfToken)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 400);
        }

        $userId = $request->request->get('user_id');
        if (!$userId) {
            return new JsonResponse(['success' => false, 'message' => 'Missing user ID'], 400);
        }

        /** @var AdminUserInterface|null $user */
        $user = $this->adminUserRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }

        $enabled = filter_var($request->request->get('enabled'), FILTER_VALIDATE_BOOLEAN);

        if (!$enabled) {
            // Disable 2FA: clear secret and flag
            $user->setGoogleAuthenticatorSecret(null);
            $user->setIsGoogleAuthenticatorEnabled(false);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'enabled' => false]);
        }

        // Enable 2FA: generate secret if not set
        if (!$user->getGoogleAuthenticatorSecret()) {
            $secret = $this->googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $this->entityManager->flush();
        }

        // Respond with URL to setup 2FA (QR scan & code verification)
        return new JsonResponse([
            'success' => true,
            'enabled' => true,
            'redirect' => $this->generateUrl('admin_2fa_setup', ['user_id' => $user->getId()]),
        ]);
    }

    /**
     * 2 & 3. Setup 2FA page: show QR code & verify code input by user.
     *    - If valid code, enable 2FA in DB.
     *    - If user already enabled 2FA, redirect to dashboard.
     */
    public function setup2fa(Request $request, Security $security): Response
    {
        $userId = $request->get('user_id');
        if (!$userId) {
            return $this->redirectToRoute('sylius_admin_dashboard');
        }

        $user = $this->adminUserRepository->find($userId);
        if (!$user instanceof AdminUserInterface) {
            return $this->redirectToRoute('sylius_admin_dashboard');
        }

        // Generate secret if missing (e.g. after reset)
        if (!$user->getGoogleAuthenticatorSecret()) {
            $secret = $this->googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $this->entityManager->flush();
        }

        $qrContent = $this->googleAuthenticator->getQRContent($user);
        $qrCode = new QrCode($qrContent);
        $qrImage = (new PngWriter())->write($qrCode)->getDataUri();

        if ($request->isMethod('POST')) {
            $code = $request->request->get('code');
            if ($this->googleAuthenticator->checkCode($user, $code)) {
                $user->setIsGoogleAuthenticatorEnabled(true);
                $this->entityManager->flush();

                $this->addFlash('success', '2FA enabled successfully.');
                return $this->redirectToRoute('sylius_admin_dashboard');
            }

            $this->addFlash('error', 'Invalid verification code.');
        }

        return $this->render('@VivanWebSolutionSylius2FAPlugin/admin/security/setup.html.twig', [
            'qrImage' => $qrImage,
            'secret' => $user->getGoogleAuthenticatorSecret(),
        ]);
    }
    public function verifyCode(Request $request, Security $security): Response
    {
        /** @var AdminUserInterface|null $user */
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('code');

            if (!$code || strlen($code) !== 6) {
                $this->addFlash('error', 'Please enter a valid 6-digit authentication code.');
                return $this->redirectToRoute('admin_2fa_verify_code');
            }

            if ($this->googleAuthenticator->checkCode($user, $code)) {
                $this->addFlash('success', 'Authentication successful.');
                $user->setIsGoogleAuthenticatorEnabled(true);
                $this->entityManager->flush();
                return $this->redirectToRoute('sylius_admin_dashboard');
            }

            $this->addFlash('error', 'Invalid authentication code. Please try again.');
        }

        return $this->render('@VivanWebSolutionSylius2FAPlugin/admin/security/verify_code.html.twig');
    }
    public function reset2FA(): Response
    {
        /** @var AdminUserInterface|null $user */
        $user = $this->getUser();

        if (!$user instanceof AdminUserInterface) {
            throw $this->createAccessDeniedException();
        }

        // Clear secret and disable 2FA
        $user->setGoogleAuthenticatorSecret(null);
        $user->setIsGoogleAuthenticatorEnabled(false);
        $this->entityManager->flush();

        $this->addFlash('success', '2FA has been reset. Please set it up again.');

        // Redirect to setup page with user ID param
        return $this->redirectToRoute('admin_2fa_setup', ['user_id' => $user->getId()]);
    }

    
}
