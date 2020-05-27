<?php
require_once("db_parms.php");
require_once("Identite.class.php");
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

       s2.target is not null as "isFollower",

       s3.blocking is not null as "blocked",

       s4.blocking is not null as "blockedYou"
       from rezozio.users
       left join rezozio.subscriptions as s1 on rezozio.users.login = s1.target and s1.follower = :current

       left join rezozio.subscriptions as s2 on rezozio.users.login = s2.follower and s2.target = :current

       left join rezozio.blockages as s3 on rezozio.users.login = s3.target and s3.blocking = :current
        left join rezozio.blockages as s4 on rezozio.users.login = s4.blocking and
        s4.target = :current
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
     *ou false si l'id de message n'existe pas ou bien si l'utilisateur n'a pas la permission de le voir. */
    public function getMessage($messageId,$current){
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
      if(!is_null($current)){
        print("il y a un utilisateur connecté ! ");
        /* on vérifie que l'utilisateur n'est pas bloqué */
        if($this->getBlockedStatus($current,$res['author']))
          $res = false;
      }
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
   public function authentifier($login, $password){ // version password hash
        $sql = <<<EOD
        select password,pseudo
        from rezozio.users
        where login = :login
EOD;
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(':login', $login);
        $stmt->execute();
        $info = $stmt->fetch();
        $res =($info && crypt($password, $info['password']) == $info['password'])?new Identite($login,$info['pseudo']):false;
        return $res;
    }
/** Crée un utilisateur, renvoie un booléen indiquant si l'opération
*s'est bien passée. */
    public function createUser($login,$password,$pseudo){
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
      $res =($stmt->rowCount() == 1)?new Identite($login,$pseudo):false;
      return $res;
    }
/** Trouve le ou les utilisateurs dont le login ou le pseudo commence par $substrin*/
    public function findUsers($substring){
    $sql = <<<EOD
    select login as "userId",pseudo
    from rezozio.users
    where login
    like :substring or pseudo like :substring;
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':substring',$substring.'%',PDO::PARAM_STR);
    $stmt->execute();
    $res = $stmt->fetchAll();
    return $res;
    }
/** trouve les messages filtrés par auteur, id de message et dans la limite de (count) messages
 *renvoie false si l'utilisateur est bloqué.*/
    public function findMessages($current,$author,$before,$count){
      if($this->getBlockedStatus($current,$author))
        return false;
      $sql =<<<EOD
      select messages.author,messages.id,messages.datetime,messages.content,users.pseudo
      from rezozio.messages join rezozio.users
      on rezozio.messages.author = rezozio.users.login
      where (:author = '' or author = :author)
      and(:before=0 or id<:before)
      order by messages.id desc
      limit :count;
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':author',$author,PDO::PARAM_STR);
      $stmt->bindValue(':before',$before,PDO::PARAM_INT);
      $stmt->bindValue(':count',$count,PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll();

    }
/** trouve les messages du fil de l'utilisateur $current filtrés par id et dans la limite de count*/
    public function findFollowedMessages($current,$before,$count){
      $sql =<<<EOD
      select id,author,content,datetime
      from rezozio.messages join rezozio.subscriptions
      on messages.author = subscriptions.target
      where subscriptions.follower = :current
      and :before=0 or id < :before
      limit :count;
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':current',$current,PDO::PARAM_STR);
    $stmt->bindValue(':before',$before,PDO::PARAM_STR);
    $stmt->bindValue(':count',$count,PDO::PARAM_STR);
    $stmt->execute();
    $res = $stmt->fetchAll();
    return $res;
    }
    /*poste un message ($source) depuis le compte $current et renvoie l'id de message
    *ou false si erreur.*/
    public function postMessage($source,$current){
      $sql =<<<EOD
      insert into rezozio.messages (content,author)
      values(:source,:current)
      returning id;
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':source',$source,PDO::PARAM_STR);
    $stmt->bindValue(':current',$current,PDO::PARAM_STR);
    $stmt->execute();
    $res  = $stmt->fetch();
    $res = (count($res)==1)?$res:false;
    return $res;
    }
/*Permet à l'utilisateur current de suivre target, renvoie un booléen indiquant
*si l'opération s'est bien passée.*/
    public function follow($current,$target)
    {
      $sql =<<<EOD
      insert into rezozio.subscriptions (follower,target)
      values(:current,:target);
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current',$current,PDO::PARAM_STR);
      $stmt->bindValue(':target',$target,PDO::PARAM_STR);
      $stmt->execute();
      return ($stmt->rowCount()==1);
    }
    /*Permet à l'utilisateur current d'unfollow target, renvoie un booléen indiquant
    *si l'opération s'est bien passée.*/
    public function unfollow($current,$target){
      $sql = <<<EOD
      delete from rezozio.subscriptions
      where follower =:current and target =:target;
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current',$current,PDO::PARAM_STR);
      $stmt->bindValue(':target',$target,PDO::PARAM_STR);
      $stmt->execute();
      return ($stmt->rowCount()==1);
    }
/*Renvoie la liste des login et peudos des followers de l'utilisateur connecté ainsi
*qu'un booléen indiquant si le suivi est réciproque*/
    public function getFollowers($current){
      $sql = <<<EOD
      select users.login as "userId", users.pseudo, t2.follower is not null as "mutual"
      from rezozio.subscriptions as t1
      left join rezozio.subscriptions as t2 on t1.follower = t2.target and t2.follower = :target
      join rezozio.users on login = t1.follower
      where t1.target = :target
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':target',$current,PDO::PARAM_STR);
      $stmt->execute();
      $res = $stmt->fetchAll();
      return $res ;
    }
/*met à jour le pseudo d'un utilisateur*/
    public function setPseudoFromProfile($current,$pseudo){
      $sql =<<<EOD
      update rezozio.users
      set pseudo =:pseudo
      where login =:current;
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':current',$current,PDO::PARAM_STR);
    $stmt->bindValue(':pseudo',$pseudo,PDO::PARAM_STR);
    $stmt->execute();
    }
/*met à jour la description d'un utilisateur*/
    public function setDescriptionFromProfile($current,$description){
      $sql =<<<EOD
      update rezozio.users
      set description =:description
      where login =:current;
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':current',$current,PDO::PARAM_STR);
    $stmt->bindValue(':description',$description,PDO::PARAM_STR);
    $stmt->execute();

    }
/*met à jour le mot de passe d'un utilisateur */
    public function setPasswordFromProfile($current,$password){
      $sql =<<<EOD
      update rezozio.users
      set password =:password
      where login =:current;
EOD;
    $print = password_hash($password,CRYPT_BLOWFISH);
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':current',$current,PDO::PARAM_STR);
    $stmt->bindValue(':password',$print,PDO::PARAM_STR);
    $stmt->execute();
    }
/** met à jour le pseudo, la description, et le mot de passe de l'utilisateur current
*renvoie false si il y a eu une erreur*/
    public function setProfile($current,$pseudo,$description,$password){
      if($pseudo !== "")
        $this->setPseudoFromProfile($current,$pseudo);
      if($description !== "")
        $this->setDescriptionFromProfile($pseudo,$description);
      if($password !== "")
        $this->setPasswordFromProfile($current,$password);

    }
    /**récupère les pseudo et login des utilisateurs auxquels l'utilisateur current est abonné */
    public function getSubscriptions($current){
      $sql =<<<EOD
      select login as "userId",pseudo
      from rezozio.users
      join rezozio.subscriptions
      on users.login = subscriptions.target
      where follower =:current;
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current',$current,PDO::PARAM_STR);
      $stmt->execute();
      $res = $stmt->fetchAll();
      return $res;
    }
/* supprime un tweet de l'utilisateur courant et renvoie un booléen indiquant si l'opération s'est bien passée*/
    public function deleteMessage($messageId,$current){
      $sql = <<<EOD
      delete from rezozio.messages
      where id =:messageId and author = :current
      returning id;
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':messageId',$messageId,PDO::PARAM_INT);
      $stmt->bindValue(':current',$current,PDO::PARAM_STR);
      $stmt->execute();
      return($stmt->fetch());
    }

/* ajoute une relation de blocage entre current (celui qui blocuqe) et target dans la table blockages
* renvoie le login de  la cible en cas de succès ou false en cas d'échec*/
    public function blockUser($current,$target){
      $sql = <<<EOD
      insert into rezozio.blockages(target,blocking)
      values(:target,:current) returning target;
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':current',$current);
    $stmt->bindValue(':target',$target);
    $stmt->execute();
    $res = $stmt->fetch();
    return $res;

    }
/*débloque un utilisateur et renvoie un booléen indiquant si l'opération s'est bien passée*/
    public function unblockUser($current,$target){
      $sql=<<<EOD
      delete from rezozio.blockages
      where target=:target and blocking=:current;
EOD;
  $stmt = $this->connexion->prepare($sql);
  $stmt->bindValue(':current',$current,PDO::PARAM_STR);
  $stmt->bindValue(':target',$target,PDO::PARAM_STR);
  $stmt->execute();
  return ($stmt->rowCount()==1);
    }
/*renvoie un booléen indiquant si l'utilisateur current est bloqué par l'utilisateur userId */
    public function getBlockedStatus($current,$userId){
      $sql = <<<EOD
      select target is not null as "blockedYou" from rezozio.blockages
      where rezozio.blockages.blocking =:userId and rezozio.blockages.target =:current;
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current',$current,PDO::PARAM_STR);
      $stmt->bindValue(':userId',$userId,PDO::PARAM_STR);
      $stmt->execute();
      $res = $stmt->fetch();
      return $res;
    }
/* supprime les messages de l'utilisateur userId*/
public function deleteMessagesFromUser($userId){
  $sql = <<<EOD
  delete from rezozio.messages
  where author = :userId;
EOD;
$stmt = $this->connexion->prepare($sql);
$stmt->bindValue(":userId",$userId,PDO::PARAM_STR);
$stmt->execute();
$res = ($stmt->rowCount() === 1);
return $res;
}
/*supprime les souscriptions de l'utilisateur userId*/
public function deleteSubscriptionsFromUser($userId){
  $sql =<<<EOD
  delete from rezozio.subscriptions
  where follower =:userId or target =:userId;
EOD;
$stmt = $this->connexion->prepare($sql);
$stmt->bindValue(":userId",$userId,PDO::PARAM_STR);
$stmt->execute();
$res = ($stmt->rowCount() === 1);
return $res;

}
/* supprime les bloquages de l'utilisateur userId*/
public function deleteBlockagesFromUser($userId){
  $sql = <<<EOD
  delete from rezozio.blockages
  where blocking =:userId or target =:userId;
EOD;
$stmt = $this->connexion->prepare($sql);
$stmt->bindValue(":userId",$userId,PDO::PARAM_STR);
$stmt->execute();
$res = ($stmt->rowCount() === 1);
return $res;
}

/*supprime le compte de l'utilisateur userId */
public function deleteProfileFromUser($userId){
  $sql =<<<EOD
        delete from rezozio.users
        where login =:userId;
EOD;
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(":userId",$userId,PDO::PARAM_STR);
        $stmt->execute();
        $res = ($stmt->rowCount() === 1);
        return $res;
}
    /*supprime l'utilisateur userId et renvoie un booléen indiquant si l'opération s'est bien passée */
    public function deleteUser($userId){
      $this->deleteMessagesFromUser($userId);
      $this->deleteSubscriptionsFromUser($userId);
      $this->deleteBlockagesFromUser($userId);
      return $this->deleteProfileFromUser($userId);
    }
}
?>
