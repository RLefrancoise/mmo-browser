type d'event (propriété "type")

- warp : pour se TP d'une map à une autre

	l'objet "data" peut contenir :

		- map 		: 	le nom de la map de destination
		- x		:	l'abcisse de la destination
		- y		:	l'ordonnée de la destination
		- direction 	:	la direction qu'aura le joueur (bas -> 0, gauche -> 1, droite -> 2, haut -> 3)

	
	il est possible de définir la propriété "direction" pour préciser la direction dans laquelle doit se trouver le
	joueur pour se TP, si rien n'est précisé, la TP se fera sans tenir compte de la direction

- script : un event scripté via un fichier javascript

	l'objet "data" peut contenir :

		- file		:	le nom du fichier contenant le script

--------------------------------------------------------------

conditions de démarrage d'event (propriété "trigger")

- player_contact :	l'évènement se déclenche lorsque le joueur marche dessus
- key_press :		l'évènement se déclenche lorsque le joueur appuie sur la touche d'action
- auto :		l'évènement se déclenche automatiquement

---------------------------------------------------------------

conditions d'apparition de l'event (propriété "conditions") : cette propriété contient un tableau d'objets définis de la
manière suivante :

	propriété "type"

- player_switch	:	une condition portant sur l'état d'un interrupteur du joueur

	l'objet "data" peut contenir :
		
		- id		:	l'identifiant de l'interrupteur
		- active	:	true si l'interrupteur doit être actif, false sinon


- server_switch	:	une condition portant sur l'état d'un interrupteur du serveur

	l'objet "data" peut contenir :
		
		- id		:	l'identifiant de l'interrupteur
		- active	:	true si l'interrupteur doit être actif, false sinon


- player_variable :	une condition portant sur la valeur d'une variable du joueur

	l'objet "data" peut contenir :

		- type		:	le type de variable

						- player_x
						- player_y
