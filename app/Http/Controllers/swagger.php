<?php

/**
 * @OA\OpenApi(
 *     @OA\Server(
 *         url="",
 *         description="API server"
 *     ),
 *     @OA\Info(
 *         version="1.0.0",
 *         title="GOERS",
 *         description="REST Api Documentation - Representational State Transfer Protocol",
 *         @OA\Contact(
 *             name="GOERS",
 *             email="contact@goers.com"
 *         ),
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         )
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     in="header",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     name="Authorization"
 * )
 *
 * @OA\Parameter(
 *     parameter="RequestedWith",
 *     name="X-Requested-With",
 *     in="header",
 *     description="Determinate as ajax request",
 *     required=false,
 *     @OA\Schema(
 *         type="string",
 *         enum={"XMLHttpRequest"},
 *         default="XMLHttpRequest"
 *     )
 * )
 *
 * @OA\Parameter(
 *     parameter="Pagination.Page",
 *     name="page",
 *     in="query",
 *     description="page number to display",
 *     required=false,
 *     @OA\Schema(
 *         type="integer",
 *         default=1
 *     ),
 *     style="form"
 * )
 *
 * @OA\Parameter(
 *     parameter="Pagination.Limit",
 *     name="limit",
 *     in="query",
 *     description="limit items per page",
 *     required=false,
 *     @OA\Schema(
 *         type="integer",
 *         default=10
 *     )
 * )
 *
 * @OA\Parameter(
 *     parameter="Sorting",
 *     name="sort",
 *     in="query",
 *     description="Sort direction",
 *     required=false,
 *     @OA\Schema(
 *         type="string",
 *         enum={"asc", "desc"},
 *         default="desc"
 *     )
 * )
 *
 * @OA\Response(
 *     response="Successful",
 *     description="Successful Operation"
 * )
 * @OA\Response(
 *     response="Unauthorized",
 *     description="Unauthorized"
 * )
 * @OA\Response(
 *     response="Forbidden",
 *     description="Forbidden"
 * )
 * @OA\Response(
 *     response="NotAllowed",
 *     description="Method Not Allowed"
 * )
 * @OA\Response(
 *     response="BadRequest",
 *     description="Bad Request"
 * )
 * @OA\Response(
 *     response="Accepted",
 *     description="Accepted"
 * )
 * @OA\Response(
 *     response="Created",
 *     description="Created"
 * )
 * @OA\Response(
 *     response="Deleted",
 *     description="Deleted"
 * )
 * @OA\Response(
 *     response="GeneralError",
 *     description="General Error"
 * )
 *
 */
