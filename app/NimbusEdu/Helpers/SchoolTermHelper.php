<?php

namespace App\Nimbus\Helpers\SchoolTerm;

use Illuminate\Support\Arr;

trait SchoolTermHelper {
  public function getSchoolTerm($termName) {
    return Arr::first($this->schoolTerms(), function($term) use ($termName){
      return $term["name"] === $termName;
    });
  }

  private function schoolTerms() {
    return config("edu.default.school_terms");
  }
}