--
-- Tabellenstruktur für Tabelle `tx_mkmailer_mailtemplates`
--
CREATE TABLE tx_mkmailer_templates (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource text NOT NULL,

    mailtype varchar(150) NOT NULL default '',
    subject varchar(255) NOT NULL default '',
    contenttext text NOT NULL,
    contenthtml text NOT NULL,
    mail_from varchar(255) NOT NULL default '',
    mail_fromName varchar(255) NOT NULL default '',
    mail_bcc varchar(255) NOT NULL default '',
    description varchar(255) NOT NULL default '',
    templatetype int(11) DEFAULT '0' NOT NULL,

    ### attachments for dam
    attachments int(11) DEFAULT '0' NOT NULL,
    ### attachments for the typo3 way
    attachmentst3 blob NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

--
-- Tabellenstruktur für Tabelle `tx_mkmailer_queue`
--
CREATE TABLE tx_mkmailer_queue (
    uid int(11) NOT NULL auto_increment,
    cr_date datetime default '0000-00-00 00:00:00',
    lastupdate datetime default '0000-00-00 00:00:00',
    deleted tinyint(4) DEFAULT '0' NOT NULL,

    isstatic tinyint(4) DEFAULT '0' NOT NULL,
    prefer tinyint(4) DEFAULT '0' NOT NULL,
    subject varchar(255) NOT NULL default '',
    contenttext text NOT NULL,
    contenthtml text NOT NULL,
    mail_from varchar(255) NOT NULL default '',
    mail_fromName varchar(255) NOT NULL default '',
    mail_cc varchar(255) NOT NULL default '',
    mail_bcc varchar(255) NOT NULL default '',
    mailcount int(11) DEFAULT '0' NOT NULL,
    attachments text NOT NULL,

    PRIMARY KEY (uid)
);

--
-- Tabellenstruktur für Tabelle `tx_mkmailer_receiver`
-- MailResolver zu einer Email
--
CREATE TABLE tx_mkmailer_receiver (
    uid int(11) NOT NULL auto_increment,
    email int(11) DEFAULT '0' NOT NULL,
    resolver varchar(255) NOT NULL default '',
    receivers text NOT NULL,
    PRIMARY KEY (uid)
);

--
-- Tabellenstruktur der Tabelle `tx_mkmailer_log`
-- Speichert versandte Mails
--
CREATE TABLE tx_mkmailer_log (
    uid int(11) NOT NULL auto_increment,
    email int(11) DEFAULT '0' NOT NULL,
    address varchar(255) NOT NULL default '',
    tstamp datetime default '0000-00-00 00:00:00',
    PRIMARY KEY (uid),
    KEY idx_mkmailer_log (email,address)
);
