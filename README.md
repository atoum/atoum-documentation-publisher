# *atoum's documentation publisher*

## Pré requis pour utiliser *atoum's documentation publisher*

Vous devez l'installer sur un serveur:

* accessible par l'extérieur, en l'occurence, les serveurs de github ;
* où vous pouvez configurer un cron.



## Installation

### Étape 1 : Récupérez le code source d'*atoum's documentation publisher*

```shell
cd /path/to/clone/this/repo
git clone http://github.com/atoum/atoum-documentation-publisher
```


### Étape 2 : Créez le fichier d'échange

```shell
cd atoum-documentation-publisher
touch flag
chmod 0777 flag
```


### Étape 3 : Faites un premier test

Lancez un navigateur et appelez l'url qui vous permet d'accéder au code d'*atoum's documentation publisher*.

Par exemple: http://monhost.com/atoum-documentation-publisher/

Si tout se passe bien, vous obtenez un message qui vous indique que la génération de la documentation a été demandée.


### Étape 4 : Configurez le cron

Créez le cron suivant :

```shell
*/10 * * * * /usr/bin/php /path/to/atoum-documentation-publisher/cron.php 2>&1 > /dev/null
```


### Étape 5 : Configurez github

Dans les hooks du repo github, ajoutez l'url que vous avez utilisez dans l'étape 3 aux "WebHook URLs".

Cliquez sur "Test hook" pour que l'url que vous avez saisie soit appelée par Github.

Ouvrez le fichier flag créé à l'étape 2, vous devriez voir l'url de votre repository dedans.



## Où sont mes docs ?

Elles se trouvent dans :
* /path/to/atoum-documentation-publisher/easybook/doc/atoum-documentation/en/Output/
* /path/to/atoum-documentation-publisher/easybook/doc/atoum-documentation/fr/Output/
