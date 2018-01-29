<?php

namespace TM;


class ConnectionMySQL
{
    /**
     * @var string
     */
    const HOST = '192.168.56.1';

    /**
     * @var string
     */
    const PORT = '3306';

    /**
     * @var string
     */
    const DB_NAME = 'tm_hyc';

    /**
     * @var string
     */
    const USERNAME = 'root';

    /**
     * @var string
     */
    const PASSWORD = '123';

    /**
     * Se conecta a la base de datos de tiem manager
     * @return \PDO
     */
    public function connect() : \PDO
    {
        try {
            return new \PDO(
                "mysql:host=".self::HOST.";dbname=".self::DB_NAME.";charset=utf8",
                self::USERNAME,
                self::PASSWORD,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (\PDOException $exception) {
            die("Error conectando al servidor: " . $exception->getMessage());
        }

    }

}