<?php

use Throwable;

class Test
{
    public
    function storeUsers($users)
    {

        foreach ($users as $user) {
            try {
                if ($user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10)
                    DB::table('users')->insert([
                        'name' => $user['name'],
                        'login' => $user['login'],
                        'email' => $user['email'],
                        'password' => md5($user['password'])
                    ]);
            } catch (Throwable $e) {
                return Redirect::back()->withErrors(['error', ['We couldn\'t store user: ' . $e->getMessage()]]);
            }
        }
        $this->sendEmail($users);
        return Redirect::back()->with(['success', 'All users created.']);
    }

    private
    function sendEmail($users)
    {
        foreach ($users as $user) {
            $message = 'Account has beed created. You can log in as <b>' . $user['login'] . '</b>';
            if ($user['email']) {
                Mail::to($user['email'])
                    ->cc('support@company.com')
                    ->subject('New account created')
                    ->queue($message);
            }
        }
        return true;
    }

    public
    function updateUsers($users)
    {
        foreach ($users as $user) {
            try {
                $password = DB::table('users')->where('id', $user['id'])->get(['password']);
                if ($user['login'] && password_verify($user['password'], $password))
                    DB::table('users')->where('id', $user['id'])->update([
                        'name' => $user['name'],
                        'email' => $user['email'],
                    ]);
            } catch (Throwable $e) {
                return Redirect::back()->withErrors(['error', ['We couldn\'t update user: ' . $e->getMessage()]]);
            }
        }
        return Redirect::back()->with(['success', 'All users updated.']);
    }
}

