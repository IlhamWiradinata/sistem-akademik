<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;

class SessionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN & LOGOUT
    |--------------------------------------------------------------------------
    |
    */

    function index()
    {
        return view('Auth.login');
    }

    function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ],[
            'email.required'=>"Email harus diisi",
            'password.required'=>"Password harus diisi",
        ]);

        $infologin = [
            'email'=>$request->email,
            'password'=>$request->password,
        ];

        if(Auth::attempt($infologin)){
            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            if(Auth::user()->role == 'Administrator'){
                return redirect('admin');
            } elseif (Auth::user()->role == 'Guru'){
                return redirect('guru');
            } elseif (Auth::user()->role == 'Siswa'){
                return redirect('siswa');
            }
        } else {
            return redirect('')->withErrors('Email dan Password yang anda masukan tidak sesuai')->withInput();
        }
    }

    function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('');
    }

    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD (LUPA PASSWORD)
    |--------------------------------------------------------------------------
    | Mengirim link reset password ke email user
    |
    */

    /**
     * Menampilkan form lupa password
     */
    public function showForgotPasswordForm()
    {
        return view('Auth.lupapassword');
    }

    /**
     * Mengirim link reset password ke email
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar di sistem',
        ]);

        // Buat token reset password
        $token = Str::random(64);

        // Simpan token ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Ambil data user
        $user = User::where('email', $request->email)->first();

        // Buat link reset password
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        // Kirim email dengan link reset password
        Mail::send('emails.forgot-password', [
            'user' => $user,
            'resetLink' => $resetLink,
            'token' => $token,
            'email' => $request->email
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Reset Kata Sandi - SMK Negeri 1 Cipeundeuy');
        });

        // Redirect kembali dengan pesan sukses
        return redirect()->route('forgot-password')
                         ->with('success', 'Link reset kata sandi telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
    }

    /**
     * Menampilkan form reset password
     */
    public function showResetPasswordForm(Request $request, $token = null)
    {
        // Validasi token
        $email = $request->email;

        // Cek apakah token valid
        $resetData = DB::table('password_reset_tokens')
                        ->where('email', $email)
                        ->first();

        if (!$resetData) {
            return redirect()->route('forgot-password')
                             ->with('error', 'Token reset password tidak valid.');
        }

        // Cek apakah token sudah kadaluarsa (60 menit)
        $createdAt = Carbon::parse($resetData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            // Hapus token yang sudah kadaluarsa
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('forgot-password')
                             ->with('error', 'Token reset password sudah kadaluarsa. Silakan minta link baru.');
        }

        return view('Auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Memproses reset password baru
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar di sistem',
            'password.required' => 'Password baru harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Cek token
        $resetData = DB::table('password_reset_tokens')
                        ->where('email', $request->email)
                        ->first();

        if (!$resetData) {
            return back()->with('error', 'Token reset password tidak valid.');
        }

        // Verifikasi token
        if (!Hash::check($request->token, $resetData->token)) {
            return back()->with('error', 'Token reset password tidak valid.');
        }

        // Cek kadaluarsa (60 menit)
        $createdAt = Carbon::parse($resetData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'Token reset password sudah kadaluarsa. Silakan minta link baru.');
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token setelah digunakan
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')
                         ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIK & SESSION
    |--------------------------------------------------------------------------
    |
    */

    public function adminStats()
    {
        $stats = [
            'total_admins' => User::where('role', 'Administrator')->count(),
            'total_gurus' => User::where('role', 'Guru')->count(),
            'total_siswas' => User::where('role', 'Siswa')->count(),
            'recent_sessions' => DB::table('sessions')
                ->where('last_activity', '>=', now()->subMinutes(30))
                ->count(),
            'total_sessions_today' => DB::table('sessions')
                ->whereDate('created_at', today())
                ->count(),
        ];

        return $stats;
    }

    public function aktifSessions()
    {
        $sessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->select('sessions.*', 'users.name', 'users.email', 'users.role')
            ->orderBy('last_activity', 'DESC')
            ->get()
            ->map(function ($session) {
                $session->last_active = Carbon::createFromTimestamp($session->last_activity);
                $session->status = now()->diffInMinutes($session->last_active) <= 5 ? 'aktif' :
                                (now()->diffInMinutes($session->last_active) <= 15 ? 'idle' : 'offline');
                $session->duration = $session->last_active->diffForHumans(null, true);
                $session->user_agent_short = Str::limit($session->user_agent, 60);
                return $session;
            });

        $stats = [
            'total' => $sessions->count(),
            'aktif' => $sessions->where('status', 'aktif')->count(),
            'idle' => $sessions->where('status', 'idle')->count(),
            'offline' => $sessions->where('status', 'offline')->count(),
            'today' => $sessions->where('last_active', '>=', today())->count(),
        ];

        return view('Admin.datamaster.session', compact('sessions', 'stats'));
    }
}
