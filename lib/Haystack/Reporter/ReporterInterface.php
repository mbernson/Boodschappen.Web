<?php namespace Haystack\Reporter;

interface ReporterInterface
{
    function reportException(\Exception $exception);
}
