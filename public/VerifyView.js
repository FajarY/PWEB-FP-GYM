import * as reqUtils from './requestTemplate.js';
import * as httpUtils from './requestUtils.js';

const usernameEl = document.getElementById('username');
const dateOfBirthEl = document.getElementById('date-of-birth');
const photoEl = document.getElementById('photo-upload');
const submitButton = document.getElementById('submit');

submitButton.addEventListener('click', async function(ev)
{
    ev.preventDefault();
    const username = usernameEl.value;
    const dateOfBirth = dateOfBirthEl.value;
    const photo = photoEl.files[0];

    if(username == '' || dateOfBirth == '' || !photo)
    {
        alert('Incomplete input!');
        return;
    }

    const reader = new FileReader();
    reader.onload = async function(ev)
    {
        const result = ev.target.result;
        const base64String = result.split(',')[1];
        const res = await reqUtils.verify(username, dateOfBirth, base64String);

        if(!res)
        {
            alert('There was an error when contacting server!');
        }
        else
        {
            if(res[0].status == httpUtils.OK)
            {
                window.location.href = '/home';
            }
            else
            {
                alert('Server rejected input, make sure the input is valid!');
            }
        }
    }

    reader.readAsDataURL(photo);
});