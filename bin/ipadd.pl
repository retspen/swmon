#!/usr/bin/perl
use Net::Telnet ();

$host = $ARGV[0];
$mac = $ARGV[1];
$vlan = $ARGV[2];
$ip = $ARGV[3];
$port = $ARGV[4];

$t=new Net::Telnet(Timeout=>20);
$t->open("$host");
$t->put("admin\n");
my $ok=$t->waitfor('/assword:/');
my $ok = 1;
if($ok) {$t->put("passwd\n");}
$t->put("config\n");
$t->put("ip source-guard binding $mac vlan $vlan $ip interface ethernet 1/$port \n");
$t->put("exit\n");
$t->put("copy running-config startup-config\n");
$t->put("\n");
$t->put("exit\n");
$t->close;
