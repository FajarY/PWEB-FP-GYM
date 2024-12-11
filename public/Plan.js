import * as reqUtils from './requestTemplate.js';
import * as httpUtils from './requestUtils.js';

var planId = '';
const planName = document.getElementById('plan-name');
const workoutDropdown = document.getElementById('workout-dropdown');
const addExerciseButton = document.getElementById('add-section-btn');
const doneButton = document.getElementById('done-btn');
const workoutSections = document.getElementById('workout-sections');

var exerciseHeadersRaw = [];
var exerciseHeaders = [];
var inputData = [];

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
planId = urlParams.get('id');

var initialized = false;

addExerciseButton.addEventListener('click', () =>
{
    addExercise();
});
doneButton.addEventListener('click', async () =>
{
    saveAllInputs();
    const res = await reqUtils.updatePlan(planId, inputData.name, inputData.exercises);
    if(res == null || res[0].status != httpUtils.OK)
    {
        alert('Failed when submiting data');
        return;
    }

    alert("Data successfully updated!");
    window.location.href = "/home";
});

function addExercise()
{
    if(!initialized) return;

    const id = workoutDropdown.value;
    for(var i = 0; i < inputData.exercises.length; i++)
    {
        if(id == inputData.exercises[i].id)
        {
            alert('Cannot add the same exercise!');
            return;
        }
    }
    if(!exerciseHeaders[id])
    {
        alert('Cannot exercise that are not in database!');
        return;
    }
    saveAllInputs();

    inputData.exercises.push(
        {
            id:id,
            sets:[]
        }
    );

    renderInputs();
}
function removeExercise(id)
{
    if(!initialized) return;

    saveAllInputs();

    var arr = [];
    for(var i = 0; i < inputData.exercises.length; i++)
    {
        const exercise = inputData.exercises[i];

        if(exercise.id == id)
        {
            continue;
        }
        arr.push(exercise);
    }

    inputData.exercises = arr;

    renderInputs();
}
function addSet(id)
{
    if(!initialized) return;

    saveAllInputs();

    for(var i = 0; i < inputData.exercises.length; i++)
    {
        const exercise = inputData.exercises[i];

        if(exercise.id == id)
        {
            exercise.sets.push(
                {
                    kg:1,
                    reps:1
                }
            );
            break;
        }
    }

    renderInputs();
}
function removeSet(id, number)
{
    console.log(`${id} ${number}`);
    if(!initialized) return;

    saveAllInputs();

    for(var i = 0; i < inputData.exercises.length; i++)
    {
        const exercise = inputData.exercises[i];

        if(exercise.id == id)
        {
            const sets = exercise.sets;
            const newSets = [];
            for(var j = 0; j < sets.length; j++)
            {
                if(j == number) continue;
                
                newSets.push(sets[j]);
            }

            inputData.exercises[i].sets = newSets;
            break;
        }
    }

    renderInputs();
}

async function intialize()
{
    const res = await reqUtils.getWorkoutPlanData(planId);
    if(res == null || res[0].status != httpUtils.OK)
    {
        alert("Error when initializing!");
        window.location.href = "/home?failedit=true";
        return;
    }
    
    inputData = res[1];
    const exRes = await reqUtils.getExerciseHeaders();
    if(exRes == null || res[0].status != httpUtils.OK)
    {
        alert("Error when initializing!");
        window.location.href = '/home?failedit=true';
        return;
    }

    exerciseHeadersRaw = exRes[1].exercises;
    var temp = [];
    for(var i = 0; i < exerciseHeadersRaw.length; i++)
    {
        temp[exerciseHeadersRaw[i].id] = exerciseHeadersRaw[i].name;
    }
    exerciseHeaders = temp;

    renderInputs();
    initialized = true;
}

function saveAllInputs()
{
    inputData.name = planName.innerHTML;
    for(var i = 0; i < inputData.exercises.length; i++)
    {
        const exercise = inputData.exercises[i];
        const sets = exercise.sets;

        for(var j = 0; j < sets.length; j++)
        {
            inputData.exercises[i].sets[j].kg = document.getElementById(`${exercise.id}-kg-${j}`).value;
            inputData.exercises[i].sets[j].reps =document.getElementById(`${exercise.id}-reps-${j}`).value;
        }
    }
}
function renderInputs()
{
    planName.innerHTML = inputData.name;
    workoutSections.innerHTML = '';
    workoutDropdown.innerHTML = '';

    const addedExercises = [];

    for(var i = 0; i < inputData.exercises.length; i++)
    {
        const exercise = inputData.exercises[i];
        addedExercises[exercise.id] = true;
        var sectionString = `
        <div class="workout-list">
            <h3>${exerciseHeaders[exercise.id]}</h3>
            <button id="${exercise.id}-delete">🗑️ Delete Section</button>
        `;
        
        const sets = exercise.sets;
        for(j = 0; j < sets.length; j++)
        {
            sectionString += `
            <div class="workout-item">
                <span>Set</span>
                <input id="${exercise.id}-kg-${j}" type="number" value="${sets[j].kg}" min="1" placeholder="kg">
                <input id="${exercise.id}-reps-${j}" type="number" value="${sets[j].reps}" min="1" placeholder="reps">
                <button id="${exercise.id}-set-del-${j}">❌ Delete</button>
            </div>
            `
        }
        sectionString += `<button id="${exercise.id}-add-set" class="add-workout-btn">➕ Add Set</button>`;
        sectionString += `</div>`;
        workoutSections.innerHTML += sectionString;
    }
    for(var i = 0; i < inputData.exercises.length; i++)
    {
        const exercise = inputData.exercises[i];
        const sets = exercise.sets;

        document.getElementById(`${exercise.id}-delete`).addEventListener('click', () =>
        {
            removeExercise(exercise.id);
        });
        document.getElementById(`${exercise.id}-add-set`).addEventListener('click', () =>
        {
            addSet(exercise.id);
        });
        for(var j = 0; j < sets.length; j++)
        {
            document.getElementById(`${exercise.id}-set-del-${j}`).addEventListener('click', (event) =>
            {
                const elementId = event.target.id;
                const parts = elementId.split('-');
                const parsedJ = parseInt(parts[parts.length - 1], 10);
                removeSet(exercise.id, parsedJ);
            })
        }
    }

    for(var i = 0; i < exerciseHeadersRaw.length; i++)
    {
        if(!addedExercises[exerciseHeadersRaw[i].id])
        {
            workoutDropdown.innerHTML += `
            <option value="${exerciseHeadersRaw[i].id}">${exerciseHeadersRaw[i].name}</option>`;
        }
    }
}

intialize();