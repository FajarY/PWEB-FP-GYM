import * as reqUtils from './requestTemplate.js';
import * as httpUtils from './requestUtils.js';

var planId = '';
const workoutContainer = document.getElementById('workout-container');
const doneButton = document.getElementById('done-btn');
const profileDisplay = document.getElementById('profile-display');
const profileName = document.getElementById('profile-name');

const planName = document.getElementById('plan-name');
const workoutTimer = document.getElementById('workout-timer');
var startTime;

var initialized = false;

var meLoaded = false;
var exerciseHeadersRaw = [];
var exerciseHeaders = [];
var inputData = [];

var checkboxes = [];

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
planId = urlParams.get('id');

function startTimer()
{
    var seconds = Math.floor((Date.now() - startTime) / 1000);
    var minutes = Math.floor(seconds / 60);
    var hour = Math.floor(minutes / 60);

    seconds -= minutes * 60;
    minutes -= hour * 60;

    workoutTimer.textContent = `${hour}:${minutes}:${seconds}`;

    setTimeout(() =>
    {
        startTimer();
    }, 100);
}

async function loadProfile()
{
    if(!meLoaded)
    {
        const me = await reqUtils.me();
        if(me != null && me[0].status == httpUtils.OK)
        {
            profileName.textContent = me[1].username.split(' ')[0].substring(0, 10);
            profileDisplay.src = `/api/user/image?id=${me[1].id}`;
            meLoaded = true;
        }
    }

    if(!meLoaded)
    {
        setTimeout(() => {
            loadProfile();
        }, 2000);
    }
}

doneButton.addEventListener('click', async () =>
{
    for(var i = 0; i < checkboxes.length; i++)
    {
        if(!document.getElementById(checkboxes[i]).checked)
        {
            alert('Workout is not yet finished!')
            return;
        }
    }

    const res = await reqUtils.createWorkoutLog(inputData.name, inputData.exercises, Date.now() - startTime);
    if(res == null || res[0].status != httpUtils.CREATED)
    {
        alert('There was an error when submitting!')
        return;
    }

    window.location.href = '/home';
});

async function initialize()
{
    const res = await reqUtils.getWorkoutPlanData(planId);
    if(res == null || res[0].status != httpUtils.OK)
    {
        alert("Error when initializing!");
        window.location.href = "/home?failtrain=true";
        return;
    }
    
    inputData = res[1];
    const exRes = await reqUtils.getExerciseHeaders();
    if(exRes == null || res[0].status != httpUtils.OK)
    {
        alert("Error when initializing!");
        window.location.href = '/home?failtrain=true';
        return;
    }

    exerciseHeadersRaw = exRes[1].exercises;
    var temp = [];
    for(var i = 0; i < exerciseHeadersRaw.length; i++)
    {
        temp[exerciseHeadersRaw[i].id] = exerciseHeadersRaw[i].name;
    }
    exerciseHeaders = temp;

    workoutContainer.innerHTML = '';
    planName.textContent = inputData.name;
    for(var i = 0; i < inputData.exercises.length; i++)
    {
        const exercise = inputData.exercises[i];
        const sets = inputData.exercises[i].sets;
        var template = `
        <!-- Mulai dari sini kotaknya -->
        <div class="flex justify-center min-h-[440px] mb-10">
        <div class="h-auto mt-10 bg-tan w-[1200px] rounded-[20px] flex flex-row shadow-custom">
            <!-- image -->
            <div class="flex w-[30%] justify-center mt-5 flex-col items-center px-10 pb-6">
            <h1 class="text-cream text-[32px] font-bold text-shadow-lg">
                ${exerciseHeaders[exercise.id]}
            </h1>
            <div class="relative w-[300px] h-[300px] flex justify-center items-center rounded-[20px]">
                <!-- Foreground Image -->
                <div class="absolute z-10 rounded-[20px] overflow-hidden h-[272px] w-[272px] shadow-md">
                <img src="/api/exercise/image?id=${exercise.id}" alt="Exercise Image" class="h-full w-full object-cover opacity-80" />
                </div>
                <!-- Background Offset -->
                <div class="absolute bg-cream h-[272px] w-[272px] rounded-[20px] top-[22px] left-[22px] z-0 shadow-custom">
                </div>
            </div>
            </div>
            <div class="w-full mt-20 ml-10">
            <div>`;
            
        for(var j = 0; j < sets.length; j++)
        {
            const checkboxId = `${exercise.id}-set-${j}`;
            checkboxes.push(checkboxId);
            template += `
            <!-- item -->
                <div class="bg-cream h-[86px] w-[730px] rounded-[20px] flex items-center justify-between mb-5">
                <!-- Kg -->
                <div class="h-full flex items-center">
                    <div
                    class="bg-tan w-[150px] h-[55px] rounded-[20px] ml-6 flex justify-center items-center font-bold text-[24px] text-cream text-shadow-lg">
                    ${sets[j].kg}
                    </div>
                    <p class="text-[32px] font-bold text-darkGray ml-5 text-shadow-lg">
                    Kg
                    </p>
                </div>
                <!-- Reps -->
                <div class="h-full flex items-center">
                    <div
                    class="bg-tan w-[150px] h-[55px] rounded-[20px] ml-6 flex justify-center items-center font-bold text-[24px] text-cream text-shadow-lg">
                    ${sets[j].reps}
                    </div>
                    <p class="text-[32px] font-bold text-darkGray ml-5 text-shadow-lg">
                    Reps
                    </p>
                </div>
                <!-- Checkbox -->
                <div class="mr-6 flex items-center w-[70px] h-[55px] bg-tan justify-center rounded-[20px]">
                    <input id="${checkboxId}" type="checkbox"
                    class="w-[50px] h-[50px] bg-tan appearance-none rounded-[5px] cursor-pointer relative" />
                </div>
                </div>`;
        }
        template += `
            </div>
            </div>
        </div>
        </div>`
        workoutContainer.innerHTML += template;
    }

    initialized = true;

    startTime = Date.now();
    startTimer();
}

loadProfile();
initialize();