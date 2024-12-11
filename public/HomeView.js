import * as reqUtils from './requestTemplate.js';
import * as httpUtils from './requestUtils.js';

const welcomeName = document.getElementById('welcome-name');
const plansList = document.getElementById('plans-list');
const addPlanButton = document.getElementById('add-plan');
const logList = document.getElementById('log-list');
const profileDisplay = document.getElementById('profile-display');
const profileName = document.getElementById('profile-name');

var meInitialized = false;
var planInitialized = false;
var logsInitialized = false;
var exerciseDataInitialized = false;

var planHeaders = [];
var logHeaders = [];
var exerciseData = [];

async function initialize()
{
    if(!exerciseDataInitialized)
    {
        const res = await reqUtils.getExerciseHeaders();
        if(res != null && res[0].status == httpUtils.OK)
        {
            for(var i = 0; i < res[1].exercises.length; i++)
            {
                exerciseData[res[1].exercises[i].id] = res[1].exercises[i].name;
            }

            exerciseDataInitialized = true;
        }
    }
    if(!meInitialized)
    {
        const me = await reqUtils.me();
        if(me != null && me[0].status == httpUtils.OK)
        {
            welcomeName.innerHTML = `Welcome ${me[1].username}!`;
            profileName.textContent = me[1].username.split(' ')[0].substring(0, 10);
            profileDisplay.src = `/api/user/image?id=${me[1].id}`;
            meInitialized = true;
        }
    }
    if(!planInitialized)
    {
        const plans = await reqUtils.getWorkoutPlanHeaders();
        if(plans != null && plans[0].status == httpUtils.OK && plans[1].plans != null && exerciseDataInitialized)
        {
            planHeaders = plans[1].plans;
            rebuildPlans(planHeaders);

            planInitialized = true;
        }
    }
    if(!logsInitialized)
    {
        const logs = await reqUtils.getWorkoutLogHeaders();
        if(logs != null && logs[0].status == httpUtils.OK && logs[1].logs != null)
        {
            logHeaders = logs[1].logs;
            await rebuildLogs(logHeaders);

            logsInitialized = true;
        }
    }

    if(!logsInitialized || !planInitialized || !meInitialized || !exerciseDataInitialized)
    {
        setTimeout(() => {
            initialize();
        }, 2000);
    }
}

function rebuildPlans(data)
{
    plansList.innerHTML = '';
    for(var i = 0; i < data.length; i++)
    {
        const now = data[i];
        var item = `
        <div class="plan bg-white">
          <span class="w-1/3">${now.name}</span>
          <span class="w-1/3">Last modified at: ${now.modified_at}</span>
          <button class="edit-btn ml-auto" onclick='window.location.href="/train?id=${now.id}"'>Start</button>
          <button class="edit-btn ml-10" onclick='window.location.href="/plan?id=${now.id}"'>✏️</button>
          <button class="ml-10" id='del-${now.id}' class="delete-btn")'>🗑️</button>
        </div>
        `;

        plansList.innerHTML += item;
    }
    for(var i = 0; i < data.length; i++)
    {
        const now = data[i];
        const delButton = document.getElementById(`del-${now.id}`);
        delButton.addEventListener('click', async () =>
        {
            deletePlan(now.id)
        });
    }
}
async function rebuildLogs(data)
{
    logList.innerHTML = '';
    for(var i = 0; i < data.length; i++)
    {
        const now = data[i];
        const logItems = await reqUtils.getWorkoutLogData(now.id);
        var item = `
        <div class="w-full bg-white border p-4 rounded-lg mb-2 shadow-md">
            <div class="w-full flex flex-row">
                <span class="w-1/3 text-left">Name ${now.name}</span>
                <span class="w-1/3 text-center">Workout Time : ${now.workout_time}</span>
                <span class="w-1/3 text-right">Complete_at : ${now.complete_at}</span>
            </div>
            <div>
        `;
        for(var j = 0; j < logItems[1].exercises.length; j++)
        {
            const logItem = logItems[1].exercises[j];
            item += `
            <div>
                <span>&emsp;${exerciseData[logItem.id]}</span><br> 
            `;

            item += '</div>'
        }
        item += '</div></div>';

        logList.innerHTML += item;
    }
}

initialize();

addPlanButton.addEventListener('click', async function(ev)
{
    const res = await reqUtils.createWorkoutPlan('New workout plan');
    
    if(res[0].status == httpUtils.CREATED)
    {
        planInitialized = false;
        await initialize();
    }
});
async function deletePlan(id)
{
    const res = await reqUtils.deleteWorkoutPlan(id);

    if(res[0].status == httpUtils.OK)
    {
        planInitialized = false;
        await initialize();
    }
}