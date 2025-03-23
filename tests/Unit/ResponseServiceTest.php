<?php

namespace Tests\Unit\Services;

use App\Models\Response;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;
use PHPUnit\Framework\TestCase;

class ResponseServiceTest extends TestCase
{
    protected $responseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responseService = new ResponseService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_throws_exception_when_non_assigned_agent_attempts_to_respond()
    {
        // Arrange
        $assignedAgentId = 1;
        $nonAssignedAgentId = 2;
        
        $agent = Mockery::mock(User::class);
        $agent->shouldReceive('getAttribute')->with('id')->andReturn($nonAssignedAgentId);
        
        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('assigned_agent_id')->andReturn($assignedAgentId);
        
        $responseData = ['content' => 'This is a test response'];
        
        // Assert & Act
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('Only the assigned agent can respond to this ticket');
        
        $this->responseService->createResponse($ticket, $agent, $responseData);
    }

    /** @test */
    public function it_gets_responses_for_a_ticket()
    {
        // Arrange
        $responses = Mockery::mock(Collection::class);
        
        $responsesRelation = Mockery::mock(HasMany::class);
        $responsesRelation->shouldReceive('with')->with(['agent:id,name,email'])->andReturnSelf();
        $responsesRelation->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf();
        $responsesRelation->shouldReceive('get')->andReturn($responses);
        
        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('responses')->andReturn($responsesRelation);
        
        // Act
        $result = $this->responseService->getTicketResponses($ticket);
        
        // Assert
        $this->assertSame($responses, $result);
    }
}
