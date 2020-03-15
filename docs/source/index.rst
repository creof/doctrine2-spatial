.. Doctrine spatial extension documentation master file, created by
   sphinx-quickstart on Fri Mar 13 12:58:42 2020.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

Welcome to Doctrine spatial extension's documentation!
======================================================

Doctrine spatial extension provides spatial types, spatial functions for doctrine. It allow you to manage
spatial entity and to store them into your database server.

Currently, doctrine spatial extension provides four dimension general geometric and geographic spatial types,
two-dimension points, linestrings, polygon and two-dimension multi-points, multi-linestrings, multi-polygons. Doctrine
spatial is only compatible with MySql and PostgreSql. For better security and better resilience of your spatial data,
we recommend that you favor the PostgreSql database server because of `the shortcomings and vulnerabilities of MySql`_.

Any help is welcomed to implement new abstracted platforms like Microsoft Sql Server.

Contents
--------

.. toctree::
   :maxdepth: 2

   Installation
   Configuration
   Entity


Search
------

* :ref:`search`

.. _the shortcomings and vulnerabilities of MySql: https://sqlpro.developpez.com/tutoriel/dangers-mysql-mariadb/