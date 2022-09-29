<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	/**
	 * @OA\Info(
	 *   title="ZSF API",
	 *   version="1.1"
	 * )
	 * 
	 * @OA\Server(
	 *   url="/api/1.1",
	 *   description="ZSF API"
	 * )
	 *
	 * @OA\SecurityScheme(
	 *   type="apiKey",
	 *   in="header",
	 *   securityScheme="header_token",
	 *   name="header_token"
	 * )
	 *
	 * @OA\SecurityScheme(
	 *   type="apiKey",
	 *   in="cookie",
	 *   securityScheme="cookie_token",
	 *   name="cookie_token"
	 * )
	 *
	 * @OA\SecurityScheme(
	 *   type="apiKey",
	 *   in="header",
	 *   securityScheme="header_token",
	 *   name="admin_header_token",
	 * )
	 *
	 * @OA\SecurityScheme(
	 *   type="apiKey",
	 *   in="cookie",
	 *   securityScheme="cookie_token",
	 *   name="admin_cookie_token"
	 * )
	 */
	class ZsfApi { }
