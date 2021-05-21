<?php

namespace App\Nimbus\Helpers\SchoolTerm;

trait SchoolTermHelper {
  public function getTermStartDate($termName) {
    return config("edu.default.school_terms.{$termName}.start_date");
  }

  public function getTermEndDate($termName) {
    return config("edu.default.school_terms.{$termName}.end_date");
  }
}