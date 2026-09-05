SELECT
    d.hostname,
    d.hardware,
    d.serial,
    d.os,
    d.snmpver,
    d.community,
    d.snmp_disable
INTO OUTFILE '/tmp/librenms_devices_final.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM devices AS d;
