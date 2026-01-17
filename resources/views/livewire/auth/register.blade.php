<div>
    {{-- The whole world belongs to you. --}}
    <!-- Step Indicator -->
    <div class="flex justify-center items-center gap-2 mb-2">
        <div class="w-3 h-3 rounded-full {{ $this->currentStep >= 1 ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-600' }}">
        </div>
        <div class="w-3 h-3 rounded-full {{ $this->currentStep >= 2 ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-600' }}">
        </div>
        <div class="w-3 h-3 rounded-full {{ $this->currentStep >= 3 ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-600' }}">
        </div>
        <div class="w-3 h-3 rounded-full {{ $this->currentStep >= 4 ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-600' }}">
        </div>
    </div>

    <!-- Form Header -->
    <div class="flex flex-col items-center gap-2 text-center">
        <div
            class="size-12 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-primary mb-2">
            <span class="material-symbols-outlined text-3xl">school</span>
        </div>
        <h1 class="text-[#0d141b] dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em]">
            @if ($this->currentStep == 1)
                Student Registration
            @elseif($this->currentStep == 2)
                Academic Details
            @elseif($this->currentStep == 3)
                Skills & Preferences
            @else
                Account Security
            @endif
        </h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
            @if ($this->currentStep == 1)
                Enter your personal information to get started
            @elseif($this->currentStep == 2)
                Tell us about your academic background
            @elseif($this->currentStep == 3)
                Share your skills and preferences
            @else
                Create a secure password for your account
            @endif
        </p>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
        <div class="bg-primary h-2 rounded-full transition-all duration-300"
            style="width: {{ ($this->currentStep / 4) * 100 }}%"></div>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Debug Section (Remove in production) -->
    {{-- @if (app()->environment('local'))
        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded text-sm">
            <strong>Debug Info:</strong>
            <div>Current Email: {{ $email }}</div>
            <div>Current Step: {{ $currentStep }}</div>
        </div>
    @endif --}}

    <!-- Error Display -->
    @error('email')
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <strong>Error:</strong> {{ $message }}
        </div>
    @enderror

    @error('general')
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <strong>Error:</strong> {{ $message }}
        </div>
    @enderror

    <!-- Form Steps -->
    <div wire:key="register-form-{{ $currentStep }}" class="flex flex-col gap-5" id="studentRegistrationForm">
        <!-- Step 1: Personal Information -->
        <div id="step-1" class="{{ $this->currentStep == 1 ? '' : 'hidden' }}" wire:key="step-1-{{ $email }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                        for="first_name">First Name</label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">person</span>
                        <input wire:model="first_name" id="first_name"
                            class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                            type="text" placeholder="John" required autofocus />
                    </div>
                    @error('first_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                        for="last_name">Last Name</label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">person</span>
                        <input wire:model="last_name" id="last_name"
                            class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                            type="text" placeholder="Doe" required />
                    </div>
                    @error('last_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal" for="email">Email
                    Address</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">mail</span>
                    <input wire:model="email" id="email"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="email" placeholder="student@example.com" required />
                </div>
                @error('email')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal" for="phone">Phone
                    Number</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">phone</span>
                    <input wire:model="phone" id="phone"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="tel" placeholder="+254 7XX XXX XXX" required />
                </div>
                @error('phone')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

              <!-- Date of Birth -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="date_of_birth">Date of Birth</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">cake</span>
                    <input wire:model="date_of_birth" id="date_of_birth"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="date" required />
                </div>
                @error('date_of_birth')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gender -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="gender">Gender</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">person</span>
                    <select wire:model="gender" id="gender"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none"
                        required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer_not_to_say">Prefer not to say</option>
                    </select>
                    <span
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                </div>
                @error('gender')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

             <!-- National ID -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="national_id">National ID Number</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">badge</span>
                    <input wire:model="national_id" id="national_id"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="text" placeholder="12345678" required />
                </div>
                @error('national_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end mt-4">
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                    wire:loading.class="btn-loading" 
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-all shadow-md hover:shadow-lg">
                    <span class="truncate">Next: Academic Details →</span>
                </button>
            </div>
        </div>

         <!-- Step 2: Academic Information -->
        <div id="step-2" class="{{ $this->currentStep == 2 ? '' : 'hidden' }}">
            <!-- Student Registration Number -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="student_reg_number">Student Registration Number</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">numbers</span>
                    <input wire:model="student_reg_number" id="student_reg_number"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="text" placeholder="ABC123456789" required />
                </div>
                @error('student_reg_number')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Institution Name -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="institution_name">Institution Name</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">location_city</span>
                    <input wire:model="institution_name" id="institution_name"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="text" placeholder="University of Nairobi" required />
                </div>
                @error('institution_name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Institution Type -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="institution_type">Institution Type</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">school</span>
                    <select wire:model="institution_type" id="institution_type"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none"
                        required>
                        <option value="">Select Institution Type</option>
                        <option value="university">University</option>
                        <option value="college">College</option>
                        <option value="polytechnic">Polytechnic</option>
                        <option value="technical">Technical Institute</option>
                    </select>
                    <span
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                </div>
                @error('institution_type')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Course Name -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="course_name">Course/Program Name</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">menu_book</span>
                    <input wire:model="course_name" id="course_name"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="text" placeholder="Bachelor of Computer Science" required />
                </div>
                @error('course_name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Course Level -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="course_level">Course Level</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">trending_up</span>
                    <select wire:model="course_level" id="course_level"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none"
                        required>
                        <option value="">Select Course Level</option>
                        <option value="certificate">Certificate</option>
                        <option value="diploma">Diploma</option>
                        <option value="bachelor">Bachelor's Degree</option>
                        <option value="masters">Master's Degree</option>
                        <option value="phd">PhD</option>
                    </select>
                    <span
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                </div>
                @error('course_level')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Year of Study -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="year_of_study">Year of Study</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">calendar_today</span>
                    <select wire:model="year_of_study" id="year_of_study"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none"
                        required>
                        <option value="">Select Year</option>
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">Year {{ $i }}</option>
                        @endfor
                    </select>
                    <span
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                </div>
                @error('year_of_study')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Expected Graduation Year -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="expected_graduation_year">Expected Graduation Year</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">event</span>
                    <select wire:model="expected_graduation_year" id="expected_graduation_year"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none"
                        required>
                        <option value="">Select Year</option>
                        @for ($year = now()->year; $year <= now()->addYears(5)->year; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                    <span
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                </div>
                @error('expected_graduation_year')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- CGPA -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="cgpa">Current CGPA</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">grade</span>
                    <input wire:model="cgpa" id="cgpa"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="number" step="0.01" min="0" max="4.0" placeholder="3.5" />
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">On a 4.0 scale (optional)</p>
                @error('cgpa')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <button type="button" wire:click="prevStep" wire:loading.attr="disabled"
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-base font-bold leading-normal tracking-[0.015em] hover:bg-gray-300 dark:hover:bg-gray-600 transition-all shadow-sm">
                    <span class="truncate">← Previous</span>
                    <span wire:loading wire:target="prevStep">
                        Loading...
                    </span>
                </button>
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                    wire:loading.class="btn-loading"
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-all shadow-md hover:shadow-lg">
                    <span class="truncate">Next: Skills →</span>
                </button>
            </div>
        </div>

         <!-- Step 3: Skills & Preferences -->
        <div id="step-3" class="{{ $this->currentStep == 3 ? '' : 'hidden' }}">
            <!-- County -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="county">County of Origin</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">map</span>
                    <select wire:model="county" id="county"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none"
                        required>
                        <option value="" disabled>Select County</option>
                        <option value="nairobi">Nairobi</option>
                        <option value="mombasa">Mombasa</option>
                        <option value="kisumu">Kisumu</option>
                        <!-- Add more counties as needed -->
                    </select>
                    <span
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                </div>
                @error('county')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Preferred Location -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="preferred_location">
                    Preffered Internship County
                </label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">location_on</span>
                    <input wire:model="preferred_location" id="preferred_location"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="text" placeholder="Nairobi, Mombasa, or Remote" />
                </div>
                @error('preferred_location')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Skills Input -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal">Skills</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-3 text-gray-400 material-symbols-outlined text-[20px]">psychology</span>
                    <div class="flex gap-2">
                        <input wire:model="skillInput" wire:keydown.enter.prevent="addSkill"
                            class="flex-1 h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                            type="text" placeholder="Type a skill and press Enter" />
                        <button wire:click="addSkill" type="button"
                            class="px-4 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                            Add
                        </button>
                    </div>
                </div>
                <!-- Display Skills -->
                @if (count($skills) > 0)
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($skills as $index => $skill)
                            <div
                                class="flex items-center gap-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-3 py-1 rounded-full text-sm">
                                {{ $skill }}
                                <button wire:click="removeSkill({{ $index }})" type="button"
                                    class="text-blue-600 hover:text-blue-800">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Interests Input -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal">
                    Additional Talents/Skills
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-400 material-symbols-outlined text-[20px]">star</span>
                    <div class="flex gap-2">
                        <input wire:model="interestInput" wire:keydown.enter.prevent="addInterest"
                            class="flex-1 h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                            type="text" placeholder="Enter your additionsl skills/talent" />
                        <button wire:click="addInterest" type="button"
                            class="px-4 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                            Add
                        </button>
                    </div>
                </div>
                <!-- Display Interests -->
                @if (count($interests) > 0)
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($interests as $index => $interest)
                            <div
                                class="flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm">
                                {{ $interest }}
                                <button wire:click="removeInterest({{ $index }})" type="button"
                                    class="text-green-600 hover:text-green-800">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Bio -->
            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                        for="bio">Short Bio</label>
                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="100 - ($wire.bio?.length || 0)">
                        {{ 100 - strlen($bio ?? '') }} characters left
                    </span>
                </div>
                <div class="relative">
                    <span
                        class="absolute left-3 top-3 text-gray-400 material-symbols-outlined text-[20px]">description</span>
                    <textarea wire:model="bio" id="bio" rows="3" maxlength="100"
                        class="w-full rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 pt-3 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all resize-none"
                        placeholder="Tell us about yourself, your career goals, and what you hope to achieve..."></textarea>
                </div>
                @error('bio')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <button type="button" wire:click="prevStep" wire:loading.attr="disabled"
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-base font-bold leading-normal tracking-[0.015em] hover:bg-gray-300 dark:hover:bg-gray-600 transition-all shadow-sm">
                    <span class="truncate">← Previous</span>
                    <span wire:loading wire:target="prevStep">
                        Loading...
                    </span>
                </button>
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                    wire:loading.class="btn-loading"
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-all shadow-md hover:shadow-lg">
                    <span class="truncate">Next: Security →</span>
                </button>
            </div>
        </div>

        <!-- Step 4: Account Security -->
        <div id="step-4" class="{{ $this->currentStep == 4 ? '' : 'hidden' }}">
            <!-- Password -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="password">Password</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">lock</span>
                    <input wire:model="password" id="password"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="password" placeholder="Create a strong password" required />
                    <button type="button" onclick="togglePassword('password')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px] cursor-pointer">visibility_off</button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum 8 characters with letters
                    and numbers</p>
                @error('password')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="flex flex-col gap-2">
                <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                    for="password_confirmation">Confirm Password</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">verified_user</span>
                    <input wire:model="password_confirmation" id="password_confirmation"
                        class="w-full h-12 rounded-lg border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-900 text-[#0d141b] dark:text-white px-3 pl-10 text-sm placeholder:text-[#91a6be] dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        type="password" placeholder="Repeat your password" required />
                    <button type="button" onclick="togglePassword('password_confirmation')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px] cursor-pointer">visibility_off</button>
                </div>
                @error('password_confirmation')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Terms and Conditions -->
            <div class="flex items-center gap-2 mt-2">
                <input wire:model="terms" id="terms" type="checkbox"
                    class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary/50 dark:bg-gray-900"
                    required />
                <label class="text-sm text-gray-600 dark:text-gray-400" for="terms">
                    I agree to the <a class="text-primary hover:underline font-medium" href="#">Terms of
                        Service</a> and <a class="text-primary hover:underline font-medium" href="#">Privacy
                        Policy</a>
                </label>
            </div>
            @error('terms')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

            <!-- Marketing Consent -->
            <div class="flex items-center gap-2 mt-2">
                <input wire:model="marketing_consent" id="marketing_consent" type="checkbox"
                    class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary/50 dark:bg-gray-900" />
                <label class="text-sm text-gray-600 dark:text-gray-400" for="marketing_consent">
                    I want to receive updates about new attachment opportunities and career tips
                </label>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-6">
                <button type="button" wire:click="prevStep" wire:loading.attr="disabled"
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-base font-bold leading-normal tracking-[0.015em] hover:bg-gray-300 dark:hover:bg-gray-600 transition-all shadow-sm">
                    <span class="truncate">← Previous</span>
                    <span wire:loading wire:target="prevStep">
                        Loading...
                    </span>
                </button>
                <button type="button" wire:click="register" wire:loading.attr="disabled"
                    wire:loading.class="btn-loading"
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-green-600 text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-green-700 transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed mt-4">
                    <span wire:loading.remove wire:target="register">
                        <span class="material-symbols-outlined mr-2">check_circle</span>
                        Create Student Account
                    </span>
                </button>
            </div>
        </div>

    </div>

</div>
