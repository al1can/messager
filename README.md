# messager
Messaging backend that can be used from any frontend client written in Symfony 6.2.

## To-Do:
- [ ] Auth with JWT
    Have problems with generating keys using 'php bin/console lexik:jwt:generate-keypair'
    Returns
    '[critical] Error thrown while running command "lexik:jwt:generate-keypair --overwrite". Message: "error:02001002:system library:fopen:No such file or directory"
    In GenerateKeyPairCommand.php line 161:
    error:02001002:system library:fopen:No such file or directory'
- [ ] Auth with phone number
- [x] Send group messages
- [ ] Write tests
- [ ] Sending files in messages
- [ ] Get the users that read the messages
- [ ] More detail on a user like pp and description
