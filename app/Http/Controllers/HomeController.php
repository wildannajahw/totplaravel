<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

use OTPHP\TOTP;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Initialise the 2FA class
        $google2fa = app('pragmarx.google2fa');
        $emailKontol;
        $secretKontol=$google2fa->generateSecretKey();
        $digits = 6;
        $digest = 'sha1';
        $period = 30;
        $totp = TOTP::create(
            $secretKontol, // New TOTP with custom secret
            30,                 // The period (int)
            'sha1',           // The digest algorithm (string)
            6                   // The number of digits (int)
        );
        $totp->now();
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $emailKontol='wildan.najah@gmail.com',
            $secretKontol
        );

        // Pass the QR barcode image to our view
        return view('google2fa.home', ['QR_Image' => $QR_Image, 'secret' => $secretKontol, 'codeOTP' => $totp->now()]);
    }
}
