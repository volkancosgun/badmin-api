<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMailSuccess;
use App\Http\Requests\ChangePasswordRequest;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends Controller
{

    public function process(ChangePasswordRequest $request)
    {
        return $this->getPasswordResetTableRow($request)->count() > 0 ? $this->changePassword($request) : $this->tokenNotFoundResponse();
    }

    private function send($request)
    {
        $user = User::whereEmail($request->email)->first();
        Mail::to($request->email)->send(new ResetPasswordMailSuccess($user->name));
    }

    private function getPasswordResetTableRow($request)
    {
        return DB::table('password_resets')->where(['email' => $request->email, 'token' => $request->resetToken]);
    }

    private function tokenNotFoundResponse()
    {
        return response()->json(['error' => 'E-mail adresi için şifre sıfırlama isteği bulunmuyor veya geçerliliğini yitirmiş.'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function changePassword($request)
    {
        $user = User::whereEmail($request->email)->first();
        $user->update([
            'password' => $request->password,
        ]);

        $this->getPasswordResetTableRow($request)->delete();
        $this->send($request);

        return response()->json(['data' => 'Şifreniz başarıyla değiştirildi'], Response::HTTP_CREATED);
    }

}
