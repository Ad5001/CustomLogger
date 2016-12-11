# CustomLogger
CustomLogger is a styliser for PocketMine servers loggers.
<br />Download the lastest version, drop it into your plugin folder, restart your server, and you will see your logger changed !
<br /><br />
# Documentation:
To customize your logger, go in the config and modify the following things:<br />
- LoggerLook for the printing messages.
- LoggerPrefix for the message you want to have just before entering your conmmand.
<br />
Replacements:
- {time} by the current H:i:s time showing
- {color} by the current prefix associated color. (§f for LoggerPrefix)
- {thread} by the thread name
- {color-(COLOR CODE)} (such as {color-b} for blue or {color-e} for yellow) by the color wanted.
- {prefix} by the prefix (could be "info", "warning", "notice"...)  (only for LoggerLook)
- {prefixLower} by the prefix but lowercased (only for LoggerLook)
- {prefixUpper} by the prefix but uppercased (only for LoggerLook)
- {message} or {msg} by the message to print (only for LoggerLook)