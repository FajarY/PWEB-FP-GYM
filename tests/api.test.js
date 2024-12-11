const cfg = require('dotenv');
const fs = require('fs');
const utils = require('./template');
const { randomInt } = require('crypto');
const { timeStamp } = require('console');

cfg.config(
    {
        path: '.env'
    }
);
cfg.config(
    {
        path: '.env.test'
    }
);

const url = process.env.url;
var token = process.env.token;
const fpdfSecret = process.env.FPDF_SECRET;
const use_cookie = process.env.use_cookie;

function createRandomizedId(maxCount, randomize)
{
    var arr = [];
    for(var i = 0; i < maxCount; i++)
    {
        arr.push(i);
    }

    for(var i = 0; i < randomize; i++)
    {
        left = randomInt(maxCount);
        right = randomInt(maxCount);

        temp = arr[left];
        arr[left] = arr[right];
        arr[right] = temp;
    }
    return arr;
}

test('[UNAUTHORIZED] Get authorized user data, [/api/me]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    const res = await fetch(url + 'api/me', req);

    expect(res.status).toBe(utils.UNAUTHORIZED);
});

var id = null;

test('[AUTHORIZED] Get authorized user data [/api/me]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + 'api/me', req);

    expect(res.status).toBe(utils.OK);

    const data = await res.json();

    expect(data).toHaveProperty('id');
    id = data.id;
    expect(data).toHaveProperty('email');
    expect(data).toHaveProperty('username');
    expect(data).toHaveProperty('date_of_birth');
    expect(data).toHaveProperty('created_at');
});

test('[UNAUTHORIZED] Get user image [/api/user/image?id={string}]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    const res = await fetch(url + `api/user/image?id=${id}`, req);

    expect(res.status).toBe(utils.UNAUTHORIZED);
});

test('[AUTHORIZED] Get user image, but not found [/api/user/image?id={string}]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + `api/user/image?id=1231434243`, req);

    expect(res.status).toBe(utils.NOT_FOUND);
});

test('[UNAUTHORIZED] Get user imageinternal, but not found [/api/user/imageinternal?id={string}&token={string}]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    const res = await fetch(url + `api/user/imageinternal?id=${id}`, req);

    expect(res.status).toBe(utils.NOT_FOUND);
});

test('[AUTHORIZED] Get user imageinternal, but not found [/api/user/imageinternal?id={string}&token={string}]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    const res = await fetch(url + `api/user/imageinternal?id=${id}&token=${fpdfSecret}`, req);

    expect(res.status).toBe(utils.OK);
});

test('[AUTHORIZED] Get user image [/api/user/image?id={string}]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + `api/user/image?id=${id}`, req);

    expect(res.status).toBe(utils.OK);
});

var exercises = [];

test('[AUTHORIZED] Get exercise headers [/api/exercise/headers]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + `api/exercise/headers`, req);

    expect(res.status).toBe(utils.OK);

    const data = await res.json();

    expect(data).toHaveProperty('exercises');

    for(var i = 0; i < data.exercises.length; i++)
    {
        const item = data.exercises[i];
        expect(item).toHaveProperty('id');
        expect(item).toHaveProperty('name');

        exercises.push(item.id);
    }
});

test('[UNAUTHORIZED] Get a exercise data [/api/exercise?id={string}]', async () =>
{
    for(var i = 0; i < exercises.length; i++)
    {
        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };

        const res = await fetch(url + `api/exercise?id=${exercises[i]}`, req);

        expect(res.status).toBe(utils.UNAUTHORIZED);
    }
});

test('[AUTHORIZED] Get a exercise data [/api/exercise?id={string}]', async () =>
{
    for(var i = 0; i < exercises.length; i++)
    {
        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/exercise?id=${exercises[i]}`, req);

        expect(res.status).toBe(utils.OK);

        const data = await res.json();

        expect(data).toHaveProperty('id');
        expect(data.id).toEqual(exercises[i]);

        expect(data).toHaveProperty('name');
        expect(data).toHaveProperty('score_multiplier');
    }
});

test('[UNAUTHORIZED] Get a exercise images [/api/exercise/image?id={string}]', async () =>
{
    for(var i = 0; i < exercises.length; i++)
    {
        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };

        const res = await fetch(url + `api/exercise/image?id=${exercises[i]}`, req);

        expect(res.status).toBe(utils.UNAUTHORIZED);
    }
});

test('[AUTHORIZED] Get a exercise images [/api/exercise/image?id={string}]', async () =>
{
    for(var i = 0; i < exercises.length; i++)
    {
        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/exercise/image?id=${exercises[i]}`, req);

        expect(res.status).toBe(utils.OK);

        const data = await res.blob();

        //Hardcoded test
        expect(data.size).toBeGreaterThan(100000);
    }
});

test('[UNAUTHORIZED] Get a exercise images internal [/api/exercise/imageinternal?id={string}&token={string}]', async () =>
{
    for(var i = 0; i < exercises.length; i++)
    {
        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };

        const res = await fetch(url + `api/exercise/imageinternal?id=${exercises[i]}`, req);

        expect(res.status).toBe(utils.NOT_FOUND);
    }
});

test('[AUTHORIZED] Get a exercise images internal [/api/exercise/imageinternal?id={string}&token={string}]', async () =>
{
    for(var i = 0; i < exercises.length; i++)
    {
        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };

        const res = await fetch(url + `api/exercise/imageinternal?id=${exercises[i]}&token=${fpdfSecret}`, req);

        expect(res.status).toBe(utils.OK);

        const data = await res.blob();

        //Hardcoded test
        expect(data.size).toBeGreaterThan(100000);
    }
});

test('[AUTHORIZED] Get a not found images [/api/exercise/image?id={string}]', async () =>
{
    for(var i = 0; i < 20; i++)
    {
        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/exercise/image?id=${crypto.randomUUID()}`, req);

        expect(res.status).toBe(utils.NOT_FOUND);
    }
});

const createPlanCount = 20;
var plans = [];

test('[AUTHORIZED] Create plan [/api/plan]', async () =>
{
    for(var i = 0; i < createPlanCount; i++)
    {
        var randomExercises = [];
        const createExCount = randomInt(2, Math.round(exercises.length / 2));
        const randomizedId = createRandomizedId(exercises.length, 40);

        for(var j = 0; j < createExCount; j++)
        {
            var sets = [];
            for(var k = 0; k < randomInt(1, 7); k++)
            {
                sets.push({
                    'reps': randomInt(1, 10),
                    'kg': randomInt(1, 100)
                });
            }

            randomExercises.push({
                'id': exercises[randomizedId[j]],
                'sets': sets
            });
        }

        const req =
        {
            method: "POST",
            headers:
            {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                'name': utils.getRandomString(32),
                'exercises': randomExercises
            })
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/plan`, req);

        expect(res.status).toBe(utils.CREATED);

        const data = await res.json();

        expect(data).toHaveProperty('id');

        plans.push(data.id);
    }
});

var planHeaders = [];
test('[AUTHORIZED] Get plan headers [/api/plan/headers]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + `api/plan/headers`, req);

    expect(res.status).toBe(utils.OK);

    const data = await res.json();

    expect(data).toHaveProperty('plans');
    expect(data.plans.length).toBeGreaterThanOrEqual(createPlanCount);

    for(var i = 0; i < data.plans.length; i++)
    {
        const item = data.plans[i];
        expect(item).toHaveProperty('id');
        expect(item).toHaveProperty('name');
        expect(item).toHaveProperty('created_at');
        expect(item).toHaveProperty('modified_at');

        planHeaders.push(item);
    }
});

test('[AUTHORIZED] Update plan [/api/plan]', async () =>
{
    for(var i = 0; i < createPlanCount; i++)
    {
        const id = plans[i];
        var randomExercises = [];
        const createExCount = randomInt(2, Math.round(exercises.length / 2));
        const randomizedId = createRandomizedId(exercises.length, 40);

        for(var j = 0; j < createExCount; j++)
        {
            var sets = [];
            for(var k = 0; k < randomInt(1, 7); k++)
            {
                sets.push({
                    'reps': randomInt(1, 10),
                    'kg': randomInt(1, 100)
                });
            }

            randomExercises.push({
                'id': exercises[randomizedId[j]],
                'sets': sets
            });
        }

        const req =
        {
            method: "PUT",
            headers:
            {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                'name': utils.getRandomString(32),
                'exercises': randomExercises
            })
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/plan?id=${id}`, req);

        expect(res.status).toBe(utils.OK);

        const data = await res.json();

        expect(data).toHaveProperty('id');
        expect(data).toHaveProperty('success');
        expect(data.success).toEqual(true);
    }
});

test('[AUTHORIZED] Get plan data [/api/plan?id={string}]', async () =>
{
    for(var i = 0; i < planHeaders.length; i++)
    {
        const id = planHeaders[i].id;

        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/plan?id=${id}`, req);

        expect(res.status).toBe(utils.OK);

        const data = await res.json();

        expect(data).toHaveProperty('id');
        expect(data).toHaveProperty('name');
        expect(data).toHaveProperty('created_at');
        expect(data).toHaveProperty('modified_at');
        expect(data).toHaveProperty('exercises');
        
        for(var j = 0; j < data.exercises.length; j++)
        {
            const item = data.exercises[j];
            expect(item).toHaveProperty('id');
            expect(item).toHaveProperty('sets');
            
            const sets = item.sets;
            for(var k = 0; k < sets.length; k++)
            {
                const setItem = sets[k];
                expect(setItem).toHaveProperty('reps');
                expect(setItem).toHaveProperty('kg');
            }
        }
    }
});

test('[AUTHORIZED] Delete plan data [/api/plan?id={string}]', async () =>
{
    for(var i = 0; i < planHeaders.length; i++)
    {
        const id = planHeaders[i].id;

        const req =
        {
            method: "DELETE",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/plan?id=${id}`, req);

        expect(res.status).toBe(utils.OK);

        const data = await res.json();

        expect(data).toHaveProperty('success');
        expect(data.success).toEqual(true);
    }
});

const createLogCount = 20;
var logs = [];
test('[AUTHORIZED] Create log [/api/log]', async () =>
{
    for(var i = 0; i < createLogCount; i++)
    {
        var randomExercises = [];
        const createExCount = randomInt(2, Math.round(exercises.length / 2));
        const randomizedId = createRandomizedId(exercises.length, 30);

        for(var j = 0; j < createExCount; j++)
        {
            var sets = [];
            for(var k = 0; k < randomInt(1, 7); k++)
            {
                sets.push({
                    'reps': randomInt(1, 10),
                    'kg': randomInt(1, 100)
                });
            }
            randomExercises.push({
                'id': exercises[randomizedId[j]],
                'sets': sets
            });
        }

        const req =
        {
            method: "POST",
            headers:
            {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                'name': utils.getRandomString(32),
                'exercises': randomExercises,
                'workout_time':randomInt(60, 60*60*12)
            })
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/log`, req);

        expect(res.status).toBe(utils.CREATED);

        const data = await res.json();

        expect(data).toHaveProperty('id');

        logs.push(data.id);
    }
});

var logsHeader = [];
test('[AUTHORIZED] Get log headers [/api/log/headers]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + `api/log/headers`, req);

    expect(res.status).toBe(utils.OK);

    const data = await res.json();

    expect(data).toHaveProperty('logs');
    expect(data.logs.length).toBeGreaterThanOrEqual(createLogCount);

    for(var i = 0; i < data.logs.length; i++)
    {
        const item = data.logs[i];
        expect(item).toHaveProperty('id');
        expect(item).toHaveProperty('name');
        expect(item).toHaveProperty('workout_time');
        expect(item).toHaveProperty('complete_at');

        logsHeader.push(item);
    }
});

test('[AUTHORIZED] Get log data [/api/log?id={string}]', async () =>
{
    for(var i = 0; i < logsHeader.length; i++)
    {
        const id = logsHeader[i].id;

        const req =
        {
            method: "GET",
            headers:
            {
                'Content-Type': 'application/json'
            }
        };
        if(use_cookie)
        {
            req.headers['Cookie'] = `token=${token};`;
        }
        else
        {
            req.headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(url + `api/log?id=${id}`, req);

        expect(res.status).toBe(utils.OK);

        const data = await res.json();

        expect(data).toHaveProperty('id');
        expect(data).toHaveProperty('name');
        expect(data).toHaveProperty('workout_time');
        expect(data).toHaveProperty('complete_at');
        expect(data).toHaveProperty('exercises');

        for(var j = 0; j < data.exercises.length; j++)
        {
            const item = data.exercises[j];
            expect(item).toHaveProperty('id');
            expect(item).toHaveProperty('sets');
            
            const sets = item.sets;
            for(var k = 0; k < sets.length; k++)
            {
                const setItem = sets[k];
                expect(setItem).toHaveProperty('reps');
                expect(setItem).toHaveProperty('kg');
            }
        }
    }
});

test('[AUTHORIZED] Get leaderboard data [/api/leaderboard]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + 'api/leaderboard', req);

    expect(res.status).toBe(utils.OK);

    const data = await res.json();

    expect(data).toHaveProperty('items');
    for(var i = 0; i < data.items.length; i++)
    {
        const item = data.items[i];
        expect(item).toHaveProperty('id');
        expect(item).toHaveProperty('username');
        expect(item).toHaveProperty('score');
    }
});

test('[UNAUTHORIZED] Get leaderboard data [/api/leaderboard]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    const res = await fetch(url + 'api/leaderboard', req);

    expect(res.status).toBe(utils.UNAUTHORIZED);
});

test('[UNAUTHORIZED] Get plan PDF [/api/plan/pdf]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    const res = await fetch(url + `api/plan/pdf`, req);

    expect(res.status).toBe(utils.UNAUTHORIZED);
});
    
test('[UNAUTHORIZED] Get log PDF [/api/log/pdf]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };

    const res = await fetch(url + `api/log/pdf`, req);

    expect(res.status).toBe(utils.UNAUTHORIZED);
});

test('[AUTHORIZED] Get plan PDF [/api/plan/pdf]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + `api/plan/pdf`, req);

    expect(res.status).toBe(utils.OK);
});

test('[AUTHORIZED] Get log PDF [/api/log/pdf]', async () =>
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type': 'application/json'
        }
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + `api/log/pdf`, req);

    expect(res.status).toBe(utils.OK);
});