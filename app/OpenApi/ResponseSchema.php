<?php

namespace App\OpenApi;

/**
 * @OA\Schema(
 *     schema="Response",
 *     type="object",
 *     @OA\Property(property="content", type="string", description="The content of the response"),
 * )
 * 
 * @OA\Schema(
 *     schema="ResponseWithDetails",
 *     allOf={@OA\Schema(ref="#/components/schemas/Response")},
 *     @OA\Property(property="id", type="integer", description="Response ID"),
 *     @OA\Property(property="ticket_id", type="integer", description="Associated ticket ID"),
 *     @OA\Property(
 *         property="agent",
 *         type="object",
 *         description="Agent who created the response",
 *         @OA\Property(property="id", type="integer", description="Agent ID"),
 *         @OA\Property(property="name", type="string", description="Agent name"),
 *         @OA\Property(property="email", type="string", description="Agent email")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp"),
 * )
 */
class ResponseSchema
{
    // This class exists only for Swagger documentation
}