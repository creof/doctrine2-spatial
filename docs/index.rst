.. Doctrine spatial extension documentation master file, created by Alexandre Tranchant

Welcome to Doctrine spatial extension's documentation!
######################################################

Doctrine spatial extension provides spatial types, spatial functions for doctrine. It allow you to manage
spatial entity and to store them into your database server.

Currently, doctrine spatial extension provides four dimension general geometric and geographic spatial types,
two-dimension points, linestrings, polygon and two-dimension multi-points, multi-linestrings, multi-polygons. Doctrine
spatial is only compatible with MySql and PostgreSql. For better security and better resilience of your spatial data,
we recommend that you favor the PostgreSql database server because of `the shortcomings and vulnerabilities of MySql`_.

Any help is welcomed to implement new abstracted platforms like Microsoft Sql Server.

Contents
********

.. toctree::
   :maxdepth: 5

   Installation
   Configuration
   Entity
   Repository
   Glossary
   Contributing

.. _the shortcomings and vulnerabilities of MySql: https://sqlpro.developpez.com/tutoriel/dangers-mysql-mariadb/