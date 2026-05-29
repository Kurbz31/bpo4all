<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Employee;
use App\Models\Attendance;

it('verifies super admin can access dashboard and management pages', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);

    $response = $this->actingAs($admin)->get('/dashboard');
    $response->assertStatus(200);

    $response = $this->actingAs($admin)->get('/users');
    $response->assertStatus(200);

    $response = $this->actingAs($admin)->get('/campaigns');
    $response->assertStatus(200);

    $response = $this->actingAs($admin)->get('/employees');
    $response->assertStatus(200);
});

it('verifies campaign creation and team leader assignment', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $leader = User::factory()->create(['role' => 'Team Leader']);

    // Create Campaign
    $campaignData = [
        'name' => 'Support Campaign',
        'description' => 'A support campaign description',
        'hours_of_work' => 8,
        'attendance_method' => Campaign::ATTENDANCE_METHOD_PRESENT_ABSENT,
        'user_ids' => [$leader->id]
    ];

    $response = $this->actingAs($admin)->post('/campaigns', $campaignData);
    $response->assertRedirect(route('campaigns.index'));

    $campaign = Campaign::where('name', 'Support Campaign')->first();
    expect($campaign)->not->toBeNull();
    expect($campaign->users->contains($leader->id))->toBeTrue();
});

it('verifies team leader campaign isolation', function () {
    $leader1 = User::factory()->create(['role' => 'Team Leader']);
    $leader2 = User::factory()->create(['role' => 'Team Leader']);

    $campaign1 = Campaign::create([
        'name' => 'Campaign One',
        'attendance_method' => Campaign::ATTENDANCE_METHOD_PRESENT_ABSENT
    ]);
    $campaign1->users()->attach($leader1);

    $campaign2 = Campaign::create([
        'name' => 'Campaign Two',
        'attendance_method' => Campaign::ATTENDANCE_METHOD_PRESENT_ABSENT
    ]);
    $campaign2->users()->attach($leader2);

    // Leader 1 should see Campaign One but not Campaign Two
    $response = $this->actingAs($leader1)->get('/campaigns');
    $response->assertStatus(200);
    $response->assertSee('Campaign One');
    $response->assertDontSee('Campaign Two');

    // Leader 2 should see Campaign Two but not Campaign One
    $response = $this->actingAs($leader2)->get('/campaigns');
    $response->assertStatus(200);
    $response->assertSee('Campaign Two');
    $response->assertDontSee('Campaign One');
});

it('verifies employee management under team leader campaigns', function () {
    $leader = User::factory()->create(['role' => 'Team Leader']);
    $campaign = Campaign::create([
        'name' => 'TL Campaign',
        'attendance_method' => Campaign::ATTENDANCE_METHOD_PRESENT_ABSENT
    ]);
    $campaign->users()->attach($leader);

    $employee = Employee::create([
        'name' => 'Agent Smith',
        'campaign_id' => $campaign->id,
        'status' => 'active'
    ]);

    // Leader should see Agent Smith
    $response = $this->actingAs($leader)->get('/employees');
    $response->assertStatus(200);
    $response->assertSee('Agent Smith');

    // Edit employee form access
    $response = $this->actingAs($leader)->get(route('employees.edit', $employee));
    $response->assertStatus(200);

    // Update employee status to terminated
    $response = $this->actingAs($leader)->put(route('employees.update', $employee), [
        'name' => 'Agent Smith Renamed',
        'campaign_id' => $campaign->id,
        'status' => 'terminated'
    ]);
    $response->assertRedirect(route('employees.terminated'));
});

it('verifies attendance flow with present/absent method', function () {
    $leader = User::factory()->create(['role' => 'Team Leader']);
    $campaign = Campaign::create([
        'name' => 'PA Campaign',
        'attendance_method' => Campaign::ATTENDANCE_METHOD_PRESENT_ABSENT
    ]);
    $campaign->users()->attach($leader);

    $employee = Employee::create([
        'name' => 'Agent John',
        'campaign_id' => $campaign->id,
        'status' => 'active'
    ]);

    // Create attendance
    $attendanceData = [
        'date' => now()->toDateString(),
        'notes' => 'Test notes',
        'targets' => [
            $employee->id => 'present'
        ]
    ];

    $response = $this->actingAs($leader)->post(route('campaigns.attendance.store', $campaign), $attendanceData);
    $response->assertRedirect(route('campaigns.show', $campaign));

    $attendance = Attendance::where('campaign_id', $campaign->id)->first();
    expect($attendance)->not->toBeNull();
    expect($attendance->targets['employees'][$employee->id])->toBe('present');

    // Edit view
    $response = $this->actingAs($leader)->get(route('campaigns.attendance.edit', [$campaign, $attendance]));
    $response->assertStatus(200);

    // Show view
    $response = $this->actingAs($leader)->get(route('campaigns.attendance.show', [$campaign, $attendance]));
    $response->assertStatus(200);

    // Update attendance
    $updateData = [
        'date' => now()->toDateString(),
        'notes' => 'Updated notes',
        'targets' => [
            $employee->id => 'absent'
        ]
    ];
    $response = $this->actingAs($leader)->put(route('campaigns.attendance.update', [$campaign, $attendance]), $updateData);
    $response->assertRedirect(route('campaigns.attendance.index', $campaign));

    $attendance->refresh();
    expect($attendance->targets['employees'][$employee->id])->toBe('absent');
});

it('verifies attendance flow with call time method', function () {
    $leader = User::factory()->create(['role' => 'Team Leader']);
    $campaign = Campaign::create([
        'name' => 'CT Campaign',
        'attendance_method' => Campaign::ATTENDANCE_METHOD_CALL_TIME,
        'minimum_call_time' => 7.5,
        'daily_salary' => 500
    ]);
    $campaign->users()->attach($leader);

    $employee = Employee::create([
        'name' => 'Agent Doe',
        'campaign_id' => $campaign->id,
        'status' => 'active'
    ]);

    // Create attendance with call time info
    $attendanceData = [
        'date' => now()->toDateString(),
        'notes' => 'Call time test',
        'targets' => [
            $employee->id => [
                'status' => 'present',
                'call_time' => '8.00',
                'daily_salary' => '500.00'
            ]
        ]
    ];

    $response = $this->actingAs($leader)->post(route('campaigns.attendance.store', $campaign), $attendanceData);
    $response->assertRedirect(route('campaigns.show', $campaign));

    $attendance = Attendance::where('campaign_id', $campaign->id)->first();
    expect($attendance)->not->toBeNull();
    expect($attendance->targets['employees'][$employee->id]['status'])->toBe('present');
    expect($attendance->targets['employees'][$employee->id]['call_time'])->toBe('8.00');

    // Update attendance
    $updateData = [
        'date' => now()->toDateString(),
        'notes' => 'Call time updated',
        'targets' => [
            $employee->id => [
                'status' => 'absent',
                'call_time' => '0',
                'daily_salary' => '0'
            ]
        ]
    ];
    $response = $this->actingAs($leader)->put(route('campaigns.attendance.update', [$campaign, $attendance]), $updateData);
    $response->assertRedirect(route('campaigns.attendance.index', $campaign));

    $attendance->refresh();
    expect($attendance->targets['employees'][$employee->id]['status'])->toBe('absent');
});
