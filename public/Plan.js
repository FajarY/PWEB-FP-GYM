import * as reqUtils from './requestTemplate.js';
import * as httpUtils from './requestUtils.js';

const welcomeName = document.getElementById('welcome-name');
const plansList = document.getElementById('plans-list');
const addPlanButton = document.getElementById('add-plan');
const logList = document.getElementById('log-list');

var meInitialized = false;
var planInitialized = false;
var logsInitialized = false;
var exerciseDataInitialized = false;

var planHeaders = [];
var logHeaders = [];
var exerciseData = [];

