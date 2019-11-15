# slack-tools
Slack tools Symfony 4 application

[![Build Status](https://travis-ci.org/jmleroux/slack-tools.svg?branch=master)](https://travis-ci.org/jmleroux/slack-tools)


https://api.slack.com/custom-integrations/legacy-tokens

### List Instant messages

List all direct messages of the owner:

```bash
./bin/console app:im:list xoxp-3054062699-3441593888-281733311666-ef15b99bea09255b23171f59e6a2b588
```

Response:

```txt
Channel ID = D88V2NDMK - User ID = U1111F55B - jm.leroux
Channel ID = D54V2N666 - User ID = U2222222B - foo.bar
[BOT] Channel ID = D666QPKM0 - User ID = U3333333J - mybot
Channel ID = D1111HDBN - User ID = U4444444V - yann.smith
```

You can find your own user ID by greping on your Slack username.

### List all your files in a channel

```bash
./bin/console app:files:list xoxp-3054062699-3441593888-281733311666-ef15b99bea09255b23171f59e6a2b588 D1111HDBN U1111F55B
```

Response:

```txt
F22222GH - 20171201_175441.jpg
F222221W - 20171129_203332.jpg
F22222S5 - 20171128_163604.jpg
```

