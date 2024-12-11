import * as reqUtils from './requestTemplate.js';
import * as httpUtils from './requestUtils.js';

const topLeaders = document.getElementById('top-leaders');
const topLeadersSecondary = document.getElementById('top-leaders-secondary');
const otherLeaders = document.getElementById('other-leaders');
const profileDisplay = document.getElementById('profile-display');
const profileName = document.getElementById('profile-name');

var leaderboardLoaded = false;
var leaderboardData = [];
var meLoaded = false;

async function buildData()
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

    if(!leaderboardLoaded)
    {
        const res = await reqUtils.getLeaderboardData();
        
        if(res != null && res[0].status == httpUtils.OK)
        {
            leaderboardData = res[1].items;

            for(var i = 0; i < leaderboardData.length; i++)
            {
                var html = '';
                if(i < 1)
                {
                    html = `
                    <div class="flex flex-col align-middle justify-start items-center m-4 border bg-white p-4 max-w-48 break-words rounded-xl shadow-md">
                        <span class="font-bold text-amber-700">👑</span>
                        <h3 class="font-bold text-amber-700 text-2xl">${i + 1}</h3>
                        <img style="width: 100px; height: 100px;" src="/api/user/image?id=${leaderboardData[i].id}" alt="${leaderboardData[i].username}" class="object-cover">
                        <span class="text-center mt-4">${leaderboardData[i].username}</span>
                        <span class="mt-4 font-bold">${leaderboardData[i].score}</span>
                    </div>
                    `;

                    topLeaders.innerHTML += html;
                }
                else if(i < 3)
                {
                    html = `
                    <div class="flex flex-col align-middle justify-start items-center m-4 border bg-white p-4 max-w-48 break-words rounded-xl shadow-md">
                        <span class="font-bold text-amber-700">👑</span>
                        <h3 class="font-bold text-amber-700 text-2xl">${i + 1}</h3>
                        <img style="width: 70px; height: 70px;" src="/api/user/image?id=${leaderboardData[i].id}" alt="${leaderboardData[i].username}" class="object-cover">
                        <span class="text-center mt-4">${leaderboardData[i].username}</span>
                        <span class="mt-4 font-bold">${leaderboardData[i].score}</span>
                    </div>
                    `;

                    topLeadersSecondary.innerHTML += html;
                }
                else
                {
                    html = `
                    <div class="w-11/12 bg-white rounded-md shadow-md flex flex-row items-center p-4 mb-4">
                        <span class="mr-4">${i + 1}. </span>
                        <img style="width: 40px; height: 40px;" class="object-cover" src="/api/user/image?id=${leaderboardData[i].id}" alt="${leaderboardData[i].username}" class="leader-pic mr-4">
                        <span class="mr-4 ml-4">${leaderboardData[i].username}</span>
                        <span class="ml-auto">${leaderboardData[i].score}</span>
                        <span class="status text-green-500">▲</span>
                    </div>
                    `;

                    otherLeaders.innerHTML += html;
                }
            }

            leaderboardLoaded = true;
        }
    }

    if(!leaderboardLoaded || !meLoaded)
    {
        setTimeout(() => {
            buildData();
        }, 2000);
    }
}

buildData();