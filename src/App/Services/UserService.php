<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    public function __construct(private Database $db)
    {
    }

    public function isEmailTaken(string $email)
    {
        $emailCount = $this->db->query("SELECT COUNT(*) FROM users WHERE email=:email", [
            'email' => $email
        ])->count();

        if ($emailCount > 0) {
            throw new ValidationException(['email' => 'Email taken']);
        }
    }

    public function create(array $data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 11]);

        $this->db->query(
            "INSERT INTO users(email,password,age,country,social_media_url)
            VALUES(:email, :password, :age, :country, :social_media_url)",
            [
                'email' => $data['email'],
                'password' => $password,
                'age' => $data['age'],
                'country' => $data['country'],
                'social_media_url' => $data['socialMediaURL']
            ]
        );

        session_regenerate_id();
        $_SESSION['user'] = $this->db->id();
    }

    public function login(array $data)
    {
        $user = $this->db->query("SELECT * FROM users WHERE email=:email", [
            'email' => $data['email']
        ])->find();

        $passwordsMatch = password_verify($data['password'], $user['password'] ?? '');

        if (!$user || !$passwordsMatch) {
            throw new ValidationException(['email' => ['Invalid credentials']]);
        }

        session_regenerate_id();

        $_SESSION['user'] = $user['id'];
    }

    public function logout()
    {
        unset($_SESSION['user']);

        session_regenerate_id();
    }
}
