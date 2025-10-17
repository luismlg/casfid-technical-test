<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Infrastructure\Service\Exception\UnauthorizedException;

/**
 * Servicio centralizado para validación de tokens JWT
 * 
 * Seguridad:
 * - No expone el JWT_SECRET en logs ni respuestas
 * - Valida firma con hash_equals para prevenir timing attacks
 * - Verifica expiración del token (exp) y not-before (nbf)
 */
final class JwtService
{
    private static ?string $jwtSecret = null;
    private static ?bool $enabled = null;

    /**
     * Inicializa la configuración JWT
     */
    public static function initialize(string $jwtSecret, bool $enabled): void
    {
        self::$jwtSecret = $jwtSecret;
        self::$enabled = $enabled;

        // Validar que existe un secret si JWT está habilitado
        if ($enabled && empty($jwtSecret)) {
            throw new \RuntimeException(
                'JWT_SECRET must be configured when JWT authentication is enabled. ' .
                'Please set JWT_SECRET in your .env file.'
            );
        }
    }

    /**
     * Valida el header Authorization
     * 
     * @throws UnauthorizedException si el token es inválido o no está presente
     */
    public static function validate(?string $authorizationHeader): void
    {
        // Si JWT está deshabilitado, permitir todas las peticiones
        if (self::$enabled === false) {
            return;
        }

        // Si JWT está habilitado pero no se ha inicializado, error
        if (self::$jwtSecret === null) {
            throw new \RuntimeException('JwtService not initialized. Call JwtService::initialize() first.');
        }

        // Verificar que el header existe y tiene formato Bearer
        if (empty($authorizationHeader)) {
            throw new UnauthorizedException(
                'Authentication required. Please provide a valid Bearer token.'
            );
        }

        if (!preg_match('/^Bearer\s+(.+)$/i', $authorizationHeader, $matches)) {
            throw new UnauthorizedException(
                'Invalid Authorization header format. Expected: Bearer <token>'
            );
        }

        $token = $matches[1];

        // Validar el token JWT
        $validationResult = self::validateToken($token);

        if (!$validationResult['valid']) {
            throw new UnauthorizedException(
                'Invalid or expired authentication token: ' . ($validationResult['reason'] ?? 'unknown')
            );
        }
    }

    /**
     * Valida un token JWT
     * 
     * @return array{valid: bool, reason?: string, claims?: array}
     */
    private static function validateToken(string $token): array
    {
        try {
            // Validar formato básico (header.payload.signature)
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                return ['valid' => false, 'reason' => 'invalid_format'];
            }

            [$headerB64, $payloadB64, $signatureB64] = $parts;

            // Decodificar y validar header
            $header = json_decode(self::base64UrlDecode($headerB64), true);
            if (!$header || !isset($header['alg'])) {
                return ['valid' => false, 'reason' => 'invalid_header'];
            }

            // Verificar algoritmo
            if ($header['alg'] !== 'HS256') {
                return ['valid' => false, 'reason' => 'unsupported_algorithm'];
            }

            // Verificar firma (timing-attack safe con hash_equals)
            $signature = self::base64UrlDecode($signatureB64);
            $expectedSignature = hash_hmac('sha256', "$headerB64.$payloadB64", self::$jwtSecret, true);

            if (!hash_equals($expectedSignature, $signature)) {
                return ['valid' => false, 'reason' => 'invalid_signature'];
            }

            // Decodificar payload
            $payload = json_decode(self::base64UrlDecode($payloadB64), true);
            if (!$payload) {
                return ['valid' => false, 'reason' => 'invalid_payload'];
            }

            // Verificar expiración (exp)
            if (isset($payload['exp'])) {
                if (!is_numeric($payload['exp']) || $payload['exp'] < time()) {
                    return ['valid' => false, 'reason' => 'token_expired'];
                }
            }

            // Verificar not-before (nbf) - token no válido aún
            if (isset($payload['nbf'])) {
                if (!is_numeric($payload['nbf']) || $payload['nbf'] > time()) {
                    return ['valid' => false, 'reason' => 'token_not_yet_valid'];
                }
            }

            return [
                'valid' => true,
                'claims' => $payload
            ];

        } catch (\Throwable $e) {
            // No exponer detalles del error por seguridad
            return ['valid' => false, 'reason' => 'validation_exception'];
        }
    }

    /**
     * Genera un token JWT
     */
    public static function generateToken(array $claims, ?int $expiresIn = 3600): string
    {
        if (self::$jwtSecret === null) {
            throw new \RuntimeException('JwtService not initialized. Call JwtService::initialize() first.');
        }

        if (empty(self::$jwtSecret)) {
            throw new \InvalidArgumentException('JWT_SECRET cannot be empty');
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $now = time();
        $payload = array_merge($claims, [
            'iat' => $now,
        ]);

        // Agregar expiración si se especifica
        if ($expiresIn !== null) {
            $payload['exp'] = $now + $expiresIn;
        }

        $headerB64 = self::base64UrlEncode(json_encode($header));
        $payloadB64 = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$headerB64.$payloadB64", self::$jwtSecret, true);
        $signatureB64 = self::base64UrlEncode($signature);

        return "$headerB64.$payloadB64.$signatureB64";
    }

    /**
     * Decodifica base64 URL-safe
     */
    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Codifica base64 URL-safe
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
