<?php

/**
 * Host LDAP with prefix ldap:// or ldaps:// for SSL
 * @example ldaps://ldap.example.org
 */
$ldap_host='ldaps://ldap.upc.edu';
define('LDAP_HOST',$ldap_host);

/**
 * Base directory for the searches
 * @example
 */
$ldap_base='ou=users,dc=upc,dc=edu';
define('LDAP_BASE',$ldap_base);

/**
 * User with full name + password for first comand
 * @example ldap_user='cn=user.name,ou=users,dc=example,dc=org'
 */
$ldap_user='cn=ldap.upc,ou=users,dc=upc,dc=edu';
define('LDAP_USER',$ldap_user);

$ldap_password='conldapnexio';
define('LDAP_PASSWORD',$ldap_password);

/**
 * Field in ldap for matching with login in estatus. Left blank if the login is exactly the CN
 */
$ldap_field_login='';
define('LDAP_FIELD_LOGIN',$ldap_field_login);

/**
 * First command for retrieving the cn of the user.
 * Variables: {FILTER},{FIELDS}
 */
$ldap_get_cn="ldapsearch -x -v -H $ldap_host -D \"$ldap_user\" -b \"$ldap_base\" -w $ldap_password \"({FILTER})\" {FIELDS}";
define('LDAP_GET_CN',$ldap_get_cn);

/**
 * Second command for testing password of the user (now we've the cn).
 * Variables: {CN},{LOGIN},{PASSWORD}
 */
$ldap_check_pass="ldapsearch -x -v -H $ldap_host -D \"cn={CN},$ldap_base\" -b \"$ldap_base\" -w {PASSWORD} \"(cn={CN},$ldap_base)\" cn";
define('LDAP_CHECK_PASS',$ldap_check_pass);
?>
