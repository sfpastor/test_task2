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
) ENGINE=InnoDB;
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

class UserEmailSenderService implements UserEmailSenderInterface 
{
    /**
     * @param string $oldEmail
     * @param string $newEmail
     *
     * @return void
     *
     * @throws EmailSendException
     */
    public function sendEmailChangedNotification(string $oldEmail, string $newEmail): void
    {
        //do email send logic
    }
  
}


class UserEmailChangerService
{
    private \PDO $db;
    
    private \UserEmailSenderService $userEmailSenderService;
 
    public function __construct(\PDO $db, \UserEmailSenderService $userEmailSenderService)
    {
      $this->db = $db;
      $this->userEmailSenderService = $userEmailSenderService;
    }
 
    /**
     * @param int $userId
     * @param string $email
     *
     * @return void
     *
     * @throws \PDOException|EmailSendException
     */
    public function changeEmail(int $userId, string $email): void
    {
      try {
        $this->db->beginTransaction();

        $statement = $this->db->prepare('SELECT id, email FROM users WHERE id = :id FOR UPDATE'); 
        $statement->bindParam(':id', $userId, \PDO::PARAM_INT);
        $statement->execute();
        $user = $stm->fetch(PDO::FETCH_ASSOC);
        
        $statement = $this->db->prepare('UPDATE users SET email = :email WHERE id = :id'); 
        $statement->bindParam(':id', $userId, \PDO::PARAM_INT);
        $statement->bindParam(':email', $email, \PDO::PARAM_STR);
        $statement->execute();

        $this->db->commit();
      } catch (\PDOException $e) {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        throw $e;
      }
      
      $this->userEmailSenderService->sendEmailChangedNotification($user['email'], $email);      
    }
}
?>