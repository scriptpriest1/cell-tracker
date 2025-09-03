<?php
// Helper function for meeting description
function getMeetingDescription($week) {
  if ($week == 1) return 'Prayer and Planning';
  if ($week == 2) return 'Bible Study Class 1';
  if ($week == 3) return 'Bible Study Class 2';
  if ($week == 4) return 'Cell Outreach';
  return 'Cell Fellowship';
}

// Helper function for report type by week
function getReportTypeByWeek($week) {
  if ($week == 4) return 'outreach';
  // week 1,2,3,5 are 'meeting'
  return 'meeting';
}