QUESTIONS:
[]findMessages => valeur vide pour l'int ??
REFACTOR:
[]nettoyer codage $args (valeurs par défaut)
[]trouver une solution plus pertinente pour la requête sql de findMessages
[]vérifier test d'existence getUser (si user s'appelle 0 par exemple)
[]png par défaut pour toutes les images
BDD:
[x]créer nouveau schéma rézozio
Statique:
[x]création page de crédits.
Libraries:
[x]commonService.php
Services obligatoires:
[x]getUser.php
[x]getProfile.php
[x]getMessage.php
[x]getAvatar.php
[x]uploadAvatar.php
[x]createUser.php
[x]findUsers.php
[x]login.php
[x]logout.php
[x]findMessages.php
[x]findFollowedMessages.php
[x]postMessage.php
[]setProfile.php
[x]follow.php
[x]unfollow.php
[x]getFollower.php
[]getSubscriptions.php
****************************
[].htacess
[] droits d'accès
****************************
Services supplémentaires:
[]removeMessage.php
[]blockUser.php
[]stories
  []addToStory.php
  //story : table avec comme colonnes : img,mimetype,datetime,user.
  //au bout de 24h, la story est supprimée de la base de données.
  []removeFromStory.php
[]images dans les messages /!\ demander au prof si c'est ok.
  //ajouter img_content, mimetype à messages (peuvent être null)
  //modifier getMessage.php
[]éditeur d'images avec filtres
  ??? js ?
[]DM
  ????
[]thème personnalisable
