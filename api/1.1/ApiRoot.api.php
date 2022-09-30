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
	 *   type="http",
	 *   scheme="bearer",
	 *   securityScheme="header_token"
	 * )
	 *
	 * @OA\SecurityScheme(
	 *   type="apiKey",
	 *   in="cookie",
	 *   securityScheme="cookie_token",
	 *   name="zsf_token"
	 * )
	 *
	 * @OA\SecurityScheme(
	 *   type="http",
	 *   scheme="bearer",
	 *   securityScheme="admin_header_token",
	 * )
	 *
	 * @OA\SecurityScheme(
	 *   type="apiKey",
	 *   in="cookie",
	 *   securityScheme="admin_cookie_token",
	 *   name="zsf_token"
	 * )
	 */
	class ZsfApi { }
