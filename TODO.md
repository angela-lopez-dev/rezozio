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
[x]getAvatar.php /!\ impossible de savoir si l'accès à l'avatar fonctionne sans uploadAvatar
[]uploadAvatar.php
[]createUser.php
[]findUsers.php
[]login.php
[]logout.php
[]findMessages.php
[]findFollowedMessages.php
[]postMessage.php
[]setProfile.php
[]follow.php
[]unfollow.php
[]getFollower.php
[]getSubscriptions.php

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
