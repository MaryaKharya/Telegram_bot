<?php

const DB_DSN = "mysql:host=us-cdbr-east-02.cleardb.com;dbname=heroku_e564b85ef073325";
const DB_USER = "b18cf3a57611ff";
const DB_PASSWORD = "db9c4d56";

function databaseConnection(): PDO
{
    static $connection = null;
    if ($connection === null)
    {
        $connection = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        $connection->query('set names utf8');
    }
    return $connection;
}

function saveUser(array $data): void
{
    $connection = databaseConnection();
    $sql = "INSERT INTO users (name, chat_id) VALUES ('{$data['message']['from']['first_name']}', '{$data['message']['chat']['id']}')";
    $connection->query($sql);
}

function getUser(string $data): array
{
    $connection = databaseConnection();
    $id = "SELECT id FROM users WHERE chat_id = {$data['message']['chat']['id']}";
    $result = $connection->query($id)->fetch();
}