<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	/**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="header_token",
 *     name="header_token"
 * )
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="cookie",
 *     securityScheme="cookie_token",
 *     name="cookie_token"
 * )
 */
class Security
{
}