<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        $data = $request->only(['name', 'email', 'message']);

        // 发送邮件（简单版本）
        Mail::raw(
            "Name: {$data['name']}\nEmail: {$data['email']}\nMessage: {$data['message']}",
            function ($mail) use ($data) {
                $mail->to('linmei7918@gmail.com') // 👈改成你的邮箱
                     ->subject('New Contact Form Message');
            }
        );

        return back()->with('success', 'Message sent successfully!');
    }
}