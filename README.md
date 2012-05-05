# Introduction: Mobile Zabbix

This project is intended as an extra application, built on top of your current [Zabbix](http://www.zabbix.com) set-up.

It connects to your Zabbix API URL, using your Zabbix credentials. Afterwards, all settings are pulled from your Zabbix.
This means any change you make in your Zabbix, will be transported to the Mobile ZBX application.

If you're worried about security implications, please read more on the FAQ at http://www.mozbx.net.

For more install guides (on your phone, or on your own servers), please see http://www.mozbx.net/install.html.

### About the configuration file

There's a file called "config-core.php" included. Copy that to "config.php" and modify it as you see fit. That file is unique to you, and won't change with future updates.

### Installation instructions

If you have a working webserver with PHP support, it's as easy as cloning the repository and browsing to the URL. By default, no special configs are needed.
You can edit the config.php (a copy of config-core.php) to substitute some variables that suite your own environment.

### Attention Zabbix 2.0 users

While MoZBX is compatible with both 1.8 and 1.9 (release candidate for 2.0), it does require a very important configuration parameter in the config.php file.

<pre>
  $arrSettings["zabbixVersionCompatibility"] = '1.8';
  $arrSettings["zabbixVersionCompatibility"] = '2.0';
</pre>

You must set the Zabbix Version accordingly or the displaying of graphs will not work.

### About the author

- Built by [Mattias Geniar](http://mattiasgeniar.be) (twitter: [@mattiasgeniar](https://twitter.com/#!/mattiasgeniar) )
- Submit your issues using [the Github issue tracker](https://github.com/mattiasgeniar/MoZBX/issues)
