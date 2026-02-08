<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StrideSync</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center relative overflow-hidden">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/Register-BG.jpg') }}');"></div>

<!-- Registration Form -->
<div class="relative z-20 bg-white/70 p-6 rounded-lg shadow-lg max-w-xl w-full text-black backdrop-blur-sm max-h-[86vh] overflow-y-auto">
    <style>
        .register-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(120, 120, 120, 0.35) transparent;
        }
        .register-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .register-scroll::-webkit-scrollbar-track {
            background: rgba(120, 120, 120, 0.2);
        }
        .register-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(120, 120, 120, 0.35);
            border-radius: 999px;
        }
    </style>
    <h2 class="text-2xl font-bold mb-4 text-center">Create an Account</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('custom.register') }}" enctype="multipart/form-data" class="space-y-3 register-scroll">
        @csrf

        <div>
            <label for="name" class="block mb-1 text-sm font-medium">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div>
            <label for="email" class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div>
            <label for="phone_number" class="block mb-1 text-sm font-medium">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required inputmode="numeric" autocomplete="tel" class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="012-345678901">
        </div>



        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

        <div>
            <label for="area" class="block mb-1 text-sm font-medium">Area</label>
            <input type="text" id="area" name="area" value="{{ old('area') }}" readonly placeholder="Tap current location button" class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        
        <button type="button" id="useRegisterLocation" class="px-3 py-1 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">Use my current location</button>
        <p id="registerGeoStatus" class="text-xs text-gray-600 mt-1"></p>

        <div>
            <label for="state" class="block mb-1 text-sm font-medium">State (Malaysia)</label>
            <select id="state" name="state" required class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="">Select your state</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="photo" class="block mb-1 text-sm font-medium">Profile Photo (optional)</label>
            <input type="file" id="photo" name="photo" accept="image/*" class="hidden">
            <label for="photo" id="photoLabel" class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black inline-block cursor-pointer">
                Choose file
            </label>
        </div>

        <div>
            <label for="password" class="block mb-1 text-sm font-medium">Password</label>
            <input type="password" id="password" name="password" required minlength="6"
                   class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400">
            <p id="password-error" class="text-red-600 text-sm mt-1 hidden">Password must be at least 6 characters.</p>
        </div>

        <div>
            <label for="password_confirmation" class="block mb-1 text-sm font-medium">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <div>
            <label for="tac_code" class="block mb-1 text-sm font-medium">TAC Code (Email)</label>
            <div class="flex gap-2">
                <input type="text" id="tac_code" name="tac_code" value="{{ old('tac_code') }}" required class="flex-1 px-4 py-2 border rounded-lg bg-white/70 text-black focus:outline-none focus:ring-2 focus:ring-green-400">
                <button type="button" id="sendRegisterTac" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition text-sm">Send TAC</button>
            </div>
            <p id="registerTacStatus" class="text-xs text-gray-600 mt-1"></p>
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Register
        </button>
    </form>

    <p class="text-center text-sm text-gray-700 mt-4">
        Already have an account? <a href="{{ route('login') }}" class="text-green-600 hover:underline">Login here</a>.
    </p>
</div>

</body>
</html>

<script>
    const regStatusEl = document.getElementById('registerGeoStatus');
    const regBtnGeo = document.getElementById('useRegisterLocation');
    const regLatInput = document.getElementById('latitude');
    const regLngInput = document.getElementById('longitude');
    const regAreaInput = document.getElementById('area');
    const regGeocodeUrl = "{{ route('reverse.geocode') }}";
    const regPhoneInput = document.getElementById('phone_number');
    const regTacBtn = document.getElementById('sendRegisterTac');
    const regTacStatus = document.getElementById('registerTacStatus');
    const regEmailInput = document.getElementById('email');
    const regNameInput = document.getElementById('name');
    const regPhotoInput = document.getElementById('photo');
    const regPhotoLabel = document.getElementById('photoLabel');
    let regReverseTimer = null;

    const setRegStatus = (msg, isError = false) => {
        if (!regStatusEl) return;
        regStatusEl.textContent = msg;
        regStatusEl.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-700');
    };

    const formatCoord = (value) => {
        const num = Number(value);
        if (!Number.isFinite(num)) return '';
        return num.toFixed(5).replace(/\.?0+$/, '');
    };

    const updateRegArea = () => {
        if (!regAreaInput) return;
        const latText = formatCoord(regLatInput?.value);
        const lngText = formatCoord(regLngInput?.value);
        if (!latText || !lngText) {
            regAreaInput.value = '';
            return;
        }
        if (latText === '0' && lngText === '0') {
            regAreaInput.value = '';
            return;
        }
        regAreaInput.value = `${latText}, ${lngText}`;
    };

    const resolveRegArea = async () => {
        const latValue = Number(regLatInput?.value);
        const lngValue = Number(regLngInput?.value);
        if (!Number.isFinite(latValue) || !Number.isFinite(lngValue)) return;

        try {
            setRegStatus('Resolving area...');
            const response = await fetch(`${regGeocodeUrl}?lat=${encodeURIComponent(latValue)}&lon=${encodeURIComponent(lngValue)}`);
            if (!response.ok) {
                setRegStatus('Unable to resolve area.', true);
                return;
            }
            const data = await response.json();
            if (data?.name && regAreaInput) {
                regAreaInput.value = data.name;
                setRegStatus('Area resolved.');
            }
        } catch (error) {
            setRegStatus('Unable to resolve area.', true);
        }
    };

    const scheduleRegReverseGeocode = () => {
        if (regReverseTimer) clearTimeout(regReverseTimer);
        regReverseTimer = setTimeout(resolveRegArea, 500);
    };

    const formatName = (value) => {
        const cleaned = (value || '').replace(/\s+/g, ' ').trim();
        if (!cleaned) return '';
        return cleaned.split(' ').map((word) => {
            return word
                .split(/([-'])/)
                .map((part) => {
                    if (part === '-' || part === "'") return part;
                    if (!part) return '';
                    return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase();
                })
                .join('');
        }).join(' ');
    };

    const applyNameFormatting = () => {
        if (!regNameInput) return;
        const formatted = formatName(regNameInput.value);
        if (formatted) regNameInput.value = formatted;
    };

    if (regNameInput) {
        regNameInput.addEventListener('blur', applyNameFormatting);
        const regForm = regNameInput.closest('form');
        if (regForm) {
            regForm.addEventListener('submit', applyNameFormatting);
        }
    }

    const fillRegFromGeo = (pos) => {
        const { latitude, longitude } = pos.coords;
        if (regLatInput) regLatInput.value = latitude.toFixed(7);
        if (regLngInput) regLngInput.value = longitude.toFixed(7);
        updateRegArea();
        scheduleRegReverseGeocode();
        setRegStatus('Location captured.');
    };

    const regGeoError = (err) => setRegStatus(`Unable to fetch location: ${err.message}`, true);

    if (regBtnGeo) {
        regBtnGeo.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setRegStatus('Geolocation is not supported in this browser.', true);
                return;
            }
            setRegStatus('Requesting location...');
            navigator.geolocation.getCurrentPosition(fillRegFromGeo, regGeoError, { enableHighAccuracy: true, timeout: 10000 });
        });
    }

    if (regLatInput) regLatInput.addEventListener('input', () => {
        updateRegArea();
        scheduleRegReverseGeocode();
    });
    if (regLngInput) regLngInput.addEventListener('input', () => {
        updateRegArea();
        scheduleRegReverseGeocode();
    });
    if (regAreaInput && (regAreaInput.value === '0,0' || regAreaInput.value === '0, 0')) {
        regAreaInput.value = '';
    }
    updateRegArea();

    const formatPhone = (value) => {
        const digits = String(value || '').replace(/\D/g, '').slice(0, 12);
        if (digits.length <= 3) return digits;
        const rest = digits.slice(3);
        if (rest.length === 0) return digits;
        return digits.slice(0, 3) + '-' + rest;
    };

    if (regPhoneInput) {
        regPhoneInput.addEventListener('input', () => {
            const digits = regPhoneInput.value.replace(/\D/g, '').slice(0, 12);
            regPhoneInput.value = formatPhone(digits);
        });
        regPhoneInput.addEventListener('blur', () => {
            regPhoneInput.value = formatPhone(regPhoneInput.value);
        });
    }

    const setTacStatus = (msg, isError = false) => {
        if (!regTacStatus) return;
        regTacStatus.textContent = msg;
        regTacStatus.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-700');
    };

    if (regTacBtn) {
        regTacBtn.addEventListener('click', async () => {
            const email = regEmailInput ? regEmailInput.value.trim() : '';
            if (!email) {
                setTacStatus('Please enter your email first.', true);
                return;
            }

            const token = document.querySelector('input[name=\"_token\"]')?.value || '';
            setTacStatus('Sending TAC...');
            try {
                const response = await fetch("{{ route('register.send-tac') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email })
                });
                const data = await response.json();
                if (data.success) {
                    setTacStatus(data.message || 'TAC sent.');
                } else {
                    setTacStatus(data.message || 'Unable to send TAC.', true);
                }
            } catch (error) {
                setTacStatus('Unable to send TAC. Please try again.', true);
            }
        });
    }

    if (regPhotoInput && regPhotoLabel) {
        regPhotoInput.addEventListener('change', () => {
            const file = regPhotoInput.files && regPhotoInput.files[0];
            regPhotoLabel.textContent = file ? file.name : 'Choose file';
        });
    }
</script>


