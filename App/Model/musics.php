<?php
namespace App\Model;

use App\Model\MainModel;
use PDO;

class musics extends MainModel
{
    protected string $primaryKey = "id";
    protected string $tableName = "musics";

    public function isAdded($name, $duration): bool
    {
        $query = "select * from musics WHERE name = '$name' and duration = $duration";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }
    public function insert_new_music($name, $file_id, $duration, $sender_id, $performer = null)
    {
        if (!($this->isAdded($name, $duration))) {
            if ($performer == null) {
                $time = time();
                $query = "insert into musics(name,file_id,duration,created_at,sender_id) value (:name,:file_id,:duration,:created_at,:sender_id)";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['name' => $name, 'file_id' => $file_id, 'duration' => $duration, 'created_at' => $time, 'sender_id' => $sender_id]);
                return $stmt->rowCount();
            } else {
                $time = time();
                $query = "insert into musics(name,file_id,singer,duration,created_at,sender_id) value (:name,:file_id,:performer,:duration,:created_at,:sender_id)";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['name' => $name, 'file_id' => $file_id, "performer" => $performer, 'duration' => $duration, 'created_at' => $time, 'sender_id' => $sender_id]);
                return $stmt->rowCount();
            }

        } else {
            return false;
        }
    }
    public function searchMusic($string)
    {

        $stmt = $this->db->prepare("SELECT * FROM musics WHERE name LIKE :keyword or singer LIKE :keyword");
        $stmt->bindValue(':keyword', "%$string%", PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function selectLastInserted()
    {
        $query = "SELECT *
        FROM musics
        ORDER BY created_at DESC
        LIMIT 10";

        $stmt = $this->db->prepare("SELECT * FROM musics WHERE name LIKE :keyword or singer LIKE :keyword");
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function selectBests()
    {
        $query = "SELECT *
        FROM musics
        ORDER BY hearts DESC
        LIMIT 10";

        $stmt = $this->db->prepare("SELECT * FROM musics WHERE name LIKE :keyword or singer LIKE :keyword");
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function CountMusic($string)
    {

        $stmt = $this->db->prepare("SELECT * FROM musics WHERE name LIKE :keyword or singer LIKE :keyword");
        $stmt->bindValue(':keyword', "%$string%", PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }

}
