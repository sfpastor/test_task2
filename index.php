<?php 
/*
Table structure: 
create table users
(
    id int auto_increment,
    name varchar(64) not null,
    email varchar(256) not null,
    constraint users_pk
        primary key (id),
    constraint users_email
        unique (email)
);
*/

interface UserEmailSenderInterface
{
    /**
     * @param string $oldEmail
     * @param string $newEmail
     *
     * @return void
     *
     * @throws EmailSendException
     */
    public function sendEmailChangedNotification(string $oldEmail, string $newEmail): void;
}


class UserEmailChangerService
{
    private \PDO $db;
 
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }
 
    /**
     * @param int $userId
     * @param string $email
     *
     * @return void
     *
     * @throws \PDOException
     */
    public function changeEmail(int $userId, string $email): void
    {
        $statement = $this->db->prepare('UPDATE users SET email = :email WHERE id = :id');
 
        $statement->bindParam(':id', $userId, \PDO::PARAM_INT);
        $statement->bindParam(':email', $email, \PDO::PARAM_STR);
        $statement->execute();
    }
}

?>