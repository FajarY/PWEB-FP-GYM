import * as utils from './requestUtils.js';

async function tryFetchJson(url, req)
{
    try
    {
        const res = await fetch(url, req);
        const data = await res.json();

        return [res, data];
    }
    catch(err)
    {
        console.error(err);
    }
}

async function verify(username, date_of_birth, profile_image)
{
    const req =
    {
        method: "POST",
        headers:
        {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'username': username,
            'date_of_birth': date_of_birth,
            'profile_image': profile_image
        })
    };
    
    return await tryFetchJson('/api/auth/verify', req);
}

async function me()
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson('/api/me', req);
}

async function getExerciseHeaders()
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson('/api/exercise/headers', req);
}

async function getExerciseData(id)
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    
    return await tryFetchJson(`/api/exercise?id=${id}`, req);
}

async function createWorkoutPlan(name)
{
    const req =
    {
        method: "POST",
        headers:
        {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'name': name,
            'exercises': []
        })
    };

    return await tryFetchJson('/api/plan', req);
}

async function updatePlan(id, name, exercise)
{
    const req =
    {
        method: "PUT",
        headers:
        {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'name': name,
            'exercises': exercise
        })
    };

    return await tryFetchJson(`/api/plan?id=${id}`, req);
}

async function getWorkoutPlanData(id)
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson(`/api/plan?id=${id}`, req);
}

async function deleteWorkoutPlan(id)
{
    const req =
    {
        method: "DELETE",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson(`/api/plan?id=${id}`, req);
}

async function getWorkoutPlanHeaders()
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson(`/api/plan/headers`, req);
}

async function createWorkoutLog(name, exercises, workout_time)
{
    const req =
    {
        method: "POST",
        headers:
        {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'name': name,
            'exercises': exercises,
            'workout_time': workout_time
        })
    };

    return await tryFetchJson(`/api/log`, req);
}

async function getWorkoutLogHeaders()
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson(`/api/log/headers`, req);
}

async function getWorkoutLogData(id)
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson(`/api/log?id=${id}`, req);
}

async function getLeaderboardData()
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    return await tryFetchJson(`/api/leaderboard`, req);
}

function readFile(file, func)
{
    const reader = new FileReader();
    reader.onload = func;

    reader.readAsDataURL(file);
}

export
{
    tryFetchJson,
    verify,
    me,
    getExerciseHeaders,
    getExerciseData,
    createWorkoutPlan,
    updatePlan,
    getWorkoutPlanData,
    getWorkoutPlanHeaders,
    deleteWorkoutPlan,
    createWorkoutLog,
    getWorkoutLogHeaders,
    getWorkoutLogData,
    getLeaderboardData
}