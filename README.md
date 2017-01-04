# SGPC
gestion website using PHPExcel (https://github.com/PHPOffice/PHPExcel)

This is an interface to maintain a list of users for an association.

It used to be maintained by hand on distributed Excel files. The web interface aim to be able to both import and export
Excel files. Each entry has to comply to some regular expressions.

The database is to be filled both from Excel and from the PHP interface.

It tries to have a separated data, view and controller design but it is not completely enforced at all!

NB: don't look at the login page, it is absolutely not meant to be secure haha!
