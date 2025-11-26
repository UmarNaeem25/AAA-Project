<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('shifts')->delete();
        DB::table('locations')->delete();
        DB::table('users')->delete();
        DB::table('roles')->delete();

        // 1. Create Roles
        $this->command->info('Creating roles...');
        $roles = [
            ['name' => 'Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cashier', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sales Associate', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Stock Clerk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Security', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cleaner', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        DB::table('roles')->insert($roles);
        $this->command->info('Created ' . count($roles) . ' roles.');

        // 2. Create Locations
        $this->command->info('Creating locations...');
        $locations = [
            [
                'name' => 'Downtown Mall', 
                'address' => '123 Main Street, City Center',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Westside Plaza', 
                'address' => '456 Oak Avenue, West District',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Eastgate Center', 
                'address' => '789 Pine Road, East Side',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Northpoint Store', 
                'address' => '321 Elm Boulevard, North End',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Southpark Retail', 
                'address' => '654 Maple Drive, South Park',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        
        DB::table('locations')->insert($locations);
        $this->command->info('Created ' . count($locations) . ' locations.');

        // 3. Create Users with different roles and weekly limits
        $this->command->info('Creating users...');
        $users = [
            // Admin User (role_id 1) - NO SHIFTS WILL BE ASSIGNED TO ADMIN
            [
                'name' => 'Admin', 
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
                'weekly_hours_limit' => 45,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Managers (role_id 1)
            [
                'name' => 'John Smith', 
                'email' => 'john.smith@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'weekly_hours_limit' => 45,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sarah Johnson', 
                'email' => 'sarah.johnson@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'weekly_hours_limit' => 40,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Supervisors (role_id 2)
            [
                'name' => 'Mike Chen', 
                'email' => 'mike.chen@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'weekly_hours_limit' => 40,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Emily Davis', 
                'email' => 'emily.davis@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'weekly_hours_limit' => 35,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Cashiers (role_id 3)
            [
                'name' => 'Robert Taylor', 
                'email' => 'robert.taylor@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 3,
                'weekly_hours_limit' => 30,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Lisa Martinez', 
                'email' => 'lisa.martinez@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 3,
                'weekly_hours_limit' => 25,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Sales Associates (role_id 4)
            [
                'name' => 'Jennifer Lee', 
                'email' => 'jennifer.lee@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 4,
                'weekly_hours_limit' => 35,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Christopher Clark', 
                'email' => 'chris.clark@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 4,
                'weekly_hours_limit' => 30,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Stock Clerks (role_id 5)
            [
                'name' => 'Kevin White', 
                'email' => 'kevin.white@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 5,
                'weekly_hours_limit' => 40,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Security (role_id 6)
            [
                'name' => 'Thomas Moore', 
                'email' => 'thomas.moore@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 6,
                'weekly_hours_limit' => 40,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Cleaners (role_id 7)
            [
                'name' => 'Nancy Thompson', 
                'email' => 'nancy.thompson@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 7,
                'weekly_hours_limit' => 35,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        
        DB::table('users')->insert($users);
        $this->command->info('Created ' . count($users) . ' users.');

        // 4. Create Shifts - Only 100 total records with constraints
        $this->command->info('Creating 100 shifts with constraints...');
        $shifts = [];
        $startDate = Carbon::now()->startOfWeek();
        
        // Track assignments and open shifts per day per location
        $assignedUsersPerDay = [];
        $openShiftsPerDayLocation = [];
        
        $totalShiftsNeeded = 100;
        $shiftsCreated = 0;
        
        // Generate shifts for 2 weeks to get approximately 100 shifts
        for ($week = 0; $week < 2 && $shiftsCreated < $totalShiftsNeeded; $week++) {
            $weekStart = $startDate->copy()->addWeeks($week);
            
            for ($day = 0; $day < 7 && $shiftsCreated < $totalShiftsNeeded; $day++) {
                $currentDate = $weekStart->copy()->addDays($day);
                $dateString = $currentDate->format('Y-m-d');
                
                // Reset assigned users for this day
                $assignedUsersPerDay[$dateString] = [];
                $openShiftsPerDayLocation[$dateString] = [];
                
                // Generate shifts for each location
                for ($locationId = 1; $locationId <= count($locations) && $shiftsCreated < $totalShiftsNeeded; $locationId++) {
                    // 1-2 shifts per location per day
                    $shiftsPerLocation = rand(1, 2);
                    
                    for ($i = 0; $i < $shiftsPerLocation && $shiftsCreated < $totalShiftsNeeded; $i++) {
                        $roleId = rand(1, count($roles));
                        
                        // Random shift times
                        $shiftType = rand(1, 3);
                        switch($shiftType) {
                            case 1: // Morning shift
                                $from = Carbon::createFromTime(6, 0, 0)->addMinutes(rand(0, 120));
                                $to = $from->copy()->addHours(rand(4, 6));
                                break;
                            case 2: // Afternoon shift
                                $from = Carbon::createFromTime(12, 0, 0)->addMinutes(rand(0, 180));
                                $to = $from->copy()->addHours(rand(4, 6));
                                break;
                            case 3: // Evening shift
                                $from = Carbon::createFromTime(16, 0, 0)->addMinutes(rand(0, 120));
                                $to = $from->copy()->addHours(rand(4, 6));
                                break;
                        }
                        
                        $duration = $from->diffInHours($to);
                        $breakTime = rand(0, 1) ? 0.5 : 0;
                        
                        // Determine if this should be an assigned shift or open shift
                        $userId = null;
                        $status = 'open';
                        
                        // Check if we already have an open shift for this location today
                        $hasOpenShiftForLocationToday = in_array($locationId, $openShiftsPerDayLocation[$dateString] ?? []);
                        
                        // Try to assign to a user if:
                        // 1. We don't already have an open shift for this location today
                        // 2. There are available users
                        // 3. 60% chance
                        if (!$hasOpenShiftForLocationToday && rand(1, 100) <= 60) {
                            // Get available users for this day (not assigned yet today and not Admin)
                            $availableUsers = [];
                            for ($userIndex = 2; $userIndex <= count($users); $userIndex++) {
                                if (!in_array($userIndex, $assignedUsersPerDay[$dateString])) {
                                    $availableUsers[] = $userIndex;
                                }
                            }
                            
                            // If there are available users, assign one randomly
                            if (!empty($availableUsers)) {
                                $userId = $availableUsers[array_rand($availableUsers)];
                                $assignedUsersPerDay[$dateString][] = $userId;
                                $status = (rand(1, 3) === 1) ? 'accepted' : 'published';
                            }
                        }
                        
                        // If no user assigned, mark as open shift and track it
                        if ($userId === null) {
                            $status = 'open';
                            // Track that this location has an open shift today
                            $openShiftsPerDayLocation[$dateString][] = $locationId;
                        }
                        
                        $shifts[] = [
                            'location_id' => $locationId,
                            'role_id' => $roleId,
                            'user_id' => $userId,
                            'date' => $dateString,
                            'from' => $from->format('H:i:s'),
                            'to' => $to->format('H:i:s'),
                            'duration' => round($duration - $breakTime, 2),
                            'break_time' => $breakTime,
                            'status' => $status,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        
                        $shiftsCreated++;
                    }
                }
            }
        }
        
        // Insert all shifts
        DB::table('shifts')->insert($shifts);
        
        // Calculate statistics
        $totalShifts = count($shifts);
        $openShifts = collect($shifts)->where('user_id', null)->count();
        $assignedShifts = $totalShifts - $openShifts;
        $adminShifts = collect($shifts)->where('user_id', 1)->count();
        
        // Verify no user has more than 1 shift per day
        $userShiftsPerDay = [];
        $violationsUser = 0;
        
        foreach ($shifts as $shift) {
            if ($shift['user_id'] !== null) {
                $key = $shift['date'] . '_' . $shift['user_id'];
                if (!isset($userShiftsPerDay[$key])) {
                    $userShiftsPerDay[$key] = 1;
                } else {
                    $userShiftsPerDay[$key]++;
                    $violationsUser++;
                }
            }
        }
        
        // Verify no same-location open shifts on same day
        $openShiftsPerDayLocationCheck = [];
        $violationsLocation = 0;
        
        foreach ($shifts as $shift) {
            if ($shift['user_id'] === null) {
                $key = $shift['date'] . '_' . $shift['location_id'];
                if (!isset($openShiftsPerDayLocationCheck[$key])) {
                    $openShiftsPerDayLocationCheck[$key] = 1;
                } else {
                    $openShiftsPerDayLocationCheck[$key]++;
                    $violationsLocation++;
                }
            }
        }
        
        $this->command->info('==========================================');
        $this->command->info('DATABASE SEEDING COMPLETED SUCCESSFULLY!');
        $this->command->info('==========================================');
        $this->command->info('Total roles created: ' . count($roles));
        $this->command->info('Total locations created: ' . count($locations));
        $this->command->info('Total users created: ' . count($users));
        $this->command->info('Total shifts created: ' . $totalShifts);
        $this->command->info('Open shifts (user_id = null): ' . $openShifts . ' (' . round(($openShifts/$totalShifts)*100, 1) . '%)');
        $this->command->info('Assigned shifts: ' . $assignedShifts . ' (' . round(($assignedShifts/$totalShifts)*100, 1) . '%)');
        $this->command->info('Shifts assigned to Admin: ' . $adminShifts . ' (should be 0)');
        $this->command->info('User constraint violations (>1 shift/day): ' . $violationsUser . ' (should be 0)');
        $this->command->info('Location constraint violations (>1 open shift/location/day): ' . $violationsLocation . ' (should be 0)');
        $this->command->info('==========================================');
        $this->command->info('Perfect for testing Hungarian Algorithm!');
        $this->command->info($openShifts . ' open shifts available for assignment.');
        $this->command->info('Admin login: admin@example.com / 12345678');
        $this->command->info('==========================================');
        
        if ($violationsUser > 0 || $violationsLocation > 0) {
            $this->command->error('WARNING: Some constraints were violated!');
        }
    }
}