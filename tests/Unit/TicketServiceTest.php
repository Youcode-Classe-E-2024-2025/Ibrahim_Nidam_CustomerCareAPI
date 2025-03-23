<?php

namespace Tests\Unit\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Repositories\TicketRepository;
use App\Services\TicketService;
use Illuminate\Support\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class TicketServiceTest extends TestCase
{
    protected $ticketRepository;
    protected $ticketService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketRepository = Mockery::mock(TicketRepository::class);
        $this->ticketService = new TicketService($this->ticketRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_ticket()
    {
        $data = [
            'subject'     => 'Test Subject',
            'description' => 'Test Description',
        ];

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(10);

        $expectedData = $data;
        $expectedData['customer_id'] = 10;
        $expectedData['status'] = 'open';

        $ticket = new Ticket();
        $this->ticketRepository
            ->shouldReceive('create')
            ->with($expectedData)
            ->once()
            ->andReturn($ticket);

        $result = $this->ticketService->createTicket($data, $user);
        $this->assertSame($ticket, $result);
    }

    public function test_get_ticket_authorized_as_customer()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(10);

        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('customer_id')->andReturn(10);
        $ticket->shouldReceive('getAttribute')->with('assigned_agent_id')->andReturn(20);

        $this->ticketRepository
            ->shouldReceive('findById')
            ->with(5)
            ->once()
            ->andReturn($ticket);

        $result = $this->ticketService->getTicket(5, $user);
        $this->assertSame($ticket, $result);
    }

    public function test_get_ticket_authorized_as_agent()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(20);

        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('customer_id')->andReturn(10);
        $ticket->shouldReceive('getAttribute')->with('assigned_agent_id')->andReturn(20);

        $this->ticketRepository
            ->shouldReceive('findById')
            ->with(5)
            ->once()
            ->andReturn($ticket);

        $result = $this->ticketService->getTicket(5, $user);
        $this->assertSame($ticket, $result);
    }


    public function test_update_ticket_as_agent()
    {
        $data = [
            'status'      => 'closed',
            'description' => 'Updated description',
            'subject'     => 'Should be filtered out for agent'
        ];

        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAgent')->andReturn(true);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(20);

        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('customer_id')->andReturn(10);
        $ticket->shouldReceive('getAttribute')->with('assigned_agent_id')->andReturn(20);

        $this->ticketRepository
            ->shouldReceive('findById')
            ->with(5)
            ->once()
            ->andReturn($ticket);

        $expectedData = [
            'status'      => 'closed',
            'description' => 'Updated description',
        ];

        $this->ticketRepository
            ->shouldReceive('update')
            ->with(5, $expectedData)
            ->once()
            ->andReturn($ticket);

        $result = $this->ticketService->updateTicket(5, $data, $user);
        $this->assertSame($ticket, $result);
    }

    public function test_update_ticket_as_customer()
    {
        $data = [
            'subject'     => 'New subject',
            'description' => 'Updated description',
            'status'      => 'closed' // should be filtered out for customer
        ];

        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAgent')->andReturn(false);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(10);

        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('customer_id')->andReturn(10);
        $ticket->shouldReceive('getAttribute')->with('assigned_agent_id')->andReturn(20);

        $this->ticketRepository
            ->shouldReceive('findById')
            ->with(5)
            ->once()
            ->andReturn($ticket);

        $expectedData = [
            'subject'     => 'New subject',
            'description' => 'Updated description',
        ];

        $this->ticketRepository
            ->shouldReceive('update')
            ->with(5, $expectedData)
            ->once()
            ->andReturn($ticket);

        $result = $this->ticketService->updateTicket(5, $data, $user);
        $this->assertSame($ticket, $result);
    }


    public function test_delete_ticket_authorized()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(10);

        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('customer_id')->andReturn(10);

        $this->ticketRepository
            ->shouldReceive('findById')
            ->with(5)
            ->once()
            ->andReturn($ticket);

        $this->ticketRepository
            ->shouldReceive('delete')
            ->with(5)
            ->once()
            ->andReturn(true);

        $result = $this->ticketService->deleteTicket(5, $user);
        $this->assertTrue($result);
    }


    public function test_get_all_tickets_for_agent()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAgent')->andReturn(true);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(20);

        $tickets = ['ticket1', 'ticket2'];

        $this->ticketRepository
            ->shouldReceive('getAllForAgent')
            ->with(20)
            ->once()
            ->andReturn($tickets);

        $result = $this->ticketService->getAllTicketsForUser($user);
        $this->assertSame($tickets, $result);
    }

    public function test_get_all_tickets_for_customer()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAgent')->andReturn(false);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(10);

        $tickets = ['ticket1', 'ticket2'];

        $this->ticketRepository
            ->shouldReceive('getAllForCustomer')
            ->with(10)
            ->once()
            ->andReturn($tickets);

        $result = $this->ticketService->getAllTicketsForUser($user);
        $this->assertSame($tickets, $result);
    }

    public function test_get_available_tickets()
    {
        $tickets = ['ticket1', 'ticket2'];

        $this->ticketRepository
            ->shouldReceive('getOpenTickets')
            ->once()
            ->andReturn($tickets);

        $result = $this->ticketService->getAvailableTickets();
        $this->assertSame($tickets, $result);
    }


    public function test_claim_ticket_authorized()
    {
        $agent = Mockery::mock(User::class);
        $agent->shouldReceive('isAgent')->andReturn(true);
        $agent->shouldReceive('getAttribute')->with('id')->andReturn(20);

        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('id')->andReturn(5);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $expectedData = [
            'assigned_agent_id' => 20,
            'status'            => 'in_progress',
            'claimed_at'        => $now,
        ];

        $this->ticketRepository
            ->shouldReceive('update')
            ->with(5, $expectedData)
            ->once()
            ->andReturn(true);

        $result = $this->ticketService->claimTicket($ticket, $agent);
        $this->assertTrue($result);
        Carbon::setTestNow(); // clear test now
    }

    public function test_get_ticket_with_agent()
    {
        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('id')->andReturn(5);

        $expected = 'ticket-with-agent';
        $this->ticketRepository
            ->shouldReceive('getWithAgent')
            ->with(5)
            ->once()
            ->andReturn($expected);

        $result = $this->ticketService->getTicketWithAgent($ticket);
        $this->assertSame($expected, $result);
    }

    public function test_get_ticket_with_responses()
    {
        $expected = 'ticket-with-responses';
        $this->ticketRepository
            ->shouldReceive('getWithResponses')
            ->with(5)
            ->once()
            ->andReturn($expected);

        $result = $this->ticketService->getTicketWithResponses(5);
        $this->assertSame($expected, $result);
    }

    public function test_resolve_ticket()
    {
        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('id')->andReturn(5);

        $this->ticketRepository
            ->shouldReceive('markAsResolved')
            ->with(5)
            ->once()
            ->andReturn(true);

        $result = $this->ticketService->resolveTicket($ticket);
        $this->assertTrue($result);
    }


    public function test_add_response_authorized()
    {
        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('id')->andReturn(5);
        $ticket->shouldReceive('getAttribute')->with('assigned_agent_id')->andReturn(20);

        $agent = Mockery::mock(User::class);
        $agent->shouldReceive('getAttribute')->with('id')->andReturn(20);

        $data = ['content' => 'Test response'];

        $expected = 'added-response';

        $this->ticketRepository
            ->shouldReceive('addResponse')
            ->with(5, [
                'content'  => 'Test response',
                'agent_id' => 20,
            ])
            ->once()
            ->andReturn($expected);

        $result = $this->ticketService->addResponse($ticket, $data, $agent);
        $this->assertSame($expected, $result);
    }

    public function test_get_ticket_with_details()
    {
        $ticket = Mockery::mock(Ticket::class);
        $ticket->shouldReceive('getAttribute')->with('id')->andReturn(5);

        $expected = 'ticket-with-details';
        $this->ticketRepository
            ->shouldReceive('getWithDetails')
            ->with(5)
            ->once()
            ->andReturn($expected);

        $result = $this->ticketService->getTicketWithDetails($ticket);
        $this->assertSame($expected, $result);
    }
}