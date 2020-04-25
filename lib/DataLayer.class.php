<?php
require_once("db_parms.php");

Class DataLayer{
    private $connexion;
    public function __construct(){

            $this->connexion = new PDO(
                       DB_DSN, DB_USER, DB_PASSWORD,
                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                       ]
                     );

    }
    /*prend en paramètre un identifiant d'utilisateur et
    renvoie le pseudo d'un utilisateur si il existe, false sinon. */
    public function getUser($userId){
      $sql = <<<EOD
      select pseudo
      from rezozio.users
      where login = :userId;
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userId',$userId);
      $stmt->execute();
      $res = $stmt->fetch();
      if($res)
        return $res;
      else
        return false;
      }
  /* prend en paramètre un identifiant d'utilisateur et éventuellement un identifiant d'utilisateur connecté et renvoie
  le profil complet (userId,pseudo,description) et éventuellement
  (followed,isFollower) en mode connecte ou bien false si l'utilisateur n'existe pas.*/
    public function getProfile($userId,$loggedInUserId=null){

      if(!is_null($loggedInUserId)){
        $sql = <<<EOD
        select
       rezozio.users.login as "userId", users.pseudo, users.description,
       s1.target is not null as "followed",

       s2.target is not null as "isFollower"
       from rezozio.users
       left join rezozio.subscriptions as s1 on rezozio.users.login = s1.target and s1.follower = :current

       left join rezozio.subscriptions as s2 on rezozio.users.login = s2.follower and s2.target = :current
       where rezozio.users.login = :userId
EOD;
    }
      else {
        $sql = <<<EOD
        select login as "userId",pseudo ,description
        from rezozio.users
        where login = :userId ;
EOD;
    }
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':userId',$userId,PDO::PARAM_STR);
    if(!is_null($loggedInUserId))
      $stmt->bindValue(':current',$loggedInUserId,PDO::PARAM_STR);
    $stmt->execute();
    $reponse = $stmt->fetch();
    $reponse = ($reponse)?$reponse:false;
    return $reponse;
    }


    /** prend en paramètre un identifiant de message et renvoie
     *les caractéristiques complètes du message (id,author,pseudo,content,datetime)
     *ou false si l'id de message n'existe pas */
    public function getMessage($messageId){
      $sql = <<<EOD
      select messages.id as "messageId",messages.author,users.pseudo,messages.content,messages.datetime
      from rezozio.messages join rezozio.users
      on messages.author = users.login
      where messages.id = :messageId;
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':messageId',$messageId,PDO::PARAM_INT);
      $stmt->execute();
      $res = $stmt->fetch();
      $res = ($res)?$res:false;
      return $res;
    }


/**  Enregistre un avatar pour l'utilisateur $userId
* paramètre $imageSpec : un tableau associatif contenant trois clés :
* 'avatar_small' : flux ouvert en lecture sur la version small
* 'avatar_large' :flux ouvert en lecture sur la version large
* 'avatar_type' : type MIME (chaîne)
* résultat : booléen indiquant si l'opération s'est bien passée
*/
public function getAvatar($login,$size){
  if($size == "small")
    $sql = "select avatar_small";
  else
    $sql = "select avatar_large";
  $sql .=<<<EOD
  ,avatar_type
  from rezozio.users
  where login=:login;
EOD;
  $stmt = $this->connexion->prepare($sql);
  $stmt->bindValue(':login',$login,PDO::PARAM_STR);
  $stmt->execute();
  $stmt->bindColumn('avatar_type',$mimetype,PDO::PARAM_STR);
  if($size == "small")
    $stmt->bindColumn('avatar_small',$flux,PDO::PARAM_LOB);
  else
    $stmt->bindColumn('avatar_large',$flux,PDO::PARAM_LOB);
  $res = $stmt->fetch();
  if($res)
      return ['data'=>$flux,'mimetype'=>$mimetype];
  else
      return $res;
}

/**prend en paramètre un identifiant d'utilisateur et
*une taille d'image ('small' ou 'large')
* et renvoie une table associative contenant deux clés:
*'data' : un flux ouvert en lecture sur les données
*'mimetype' : type mime (chaîne).
*ou false si l'identifiant est incorrect.
*/
    public function storeAvatar($imageSpec,$login)
  {
      $flux_small = $imageSpec['avatar_small'];
      $flux_large = $imageSpec['avatar_large'];
      $type = $imageSpec['mimetype'];
      $sql = <<<EOD
      update rezozio.users
      set avatar_type=:avatarType,
          avatar_small=:avatarSmall,
          avatar_large=:avatarLarge
      where login =:userId;
EOD;
      $insertion = $this->connexion->prepare($sql);
      $insertion->bindValue(":avatarType",$type,PDO::PARAM_STR);
      $insertion->bindValue(":avatarSmall",$flux_small,PDO::PARAM_LOB);
      $insertion->bindValue(":avatarLarge",$flux_large,PDO::PARAM_LOB);
      $insertion->bindValue(":userId",$login,PDO::PARAM_STR);
      $insertion->execute();
      $res = ($insertion->rowCount()==1);
      return $res;
}
/*
    * Test d'authentification
    * $login, $password : authentifiants
    * résultat : un booléen indiquant si l'authentification a réussi.
    */
   function authentifier($login, $password){ // version password hash
        $sql = <<<EOD
        select password
        from rezozio.users
        where login = :login
EOD;
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(':login', $login);
        $stmt->execute();
        $info = $stmt->fetch();
        return ($info && crypt($password, $info['password']) == $info['password']);
    }
/** Crée un utilisateur, renvoie un booléen indiquant si l'opération
*s'est bien passée. */
    function createUser($login,$password,$pseudo){
      $print = password_hash($password,CRYPT_BLOWFISH);
      $sql =<<<EOD
      insert into rezozio.users(login,password,pseudo)
      select :login::text,:password,:pseudo
      where not exists(select login from rezozio.users where login =:login::text);
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login',$login,PDO::PARAM_STR);
      $stmt->bindValue(':password',$print,PDO::PARAM_STR);
      $stmt->bindValue(':pseudo',$pseudo,PDO::PARAM_STR);
      $stmt->execute();
      return ($stmt->rowCount() == 1);
    }

    function findUsers($substring){
    $sql = <<<EOD
    select login,pseudo
    from rezozio.user
    where login
    like :substring;
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':substring',$substring.'%',PDO::PARAM_STR);
    $stmt->execute();
    $res = $stmt->fetchAll();
    return $res;
    }

}
?>
