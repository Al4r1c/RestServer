@echo off

SET var=

IF /%1/ == /coverage/ GOTO setcoverage
GOTO alltest


:setcoverage
ECHO Coverage actif
RD %~dp0\report /S /Q
SET var=--coverage-html %~dp0\report
GOTO alltest


:alltest
CALL phpunit %var% %~dp0\AllTest.php
GOTO fin

:fin
IF /%1/ == /coverage/ START %~dp0\report\index.html