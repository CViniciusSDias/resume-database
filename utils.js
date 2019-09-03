// fetch('institutions.php').then((data) => {
//     data.json().then((parsedJson) => {
//         parsedJson.forEach((e, i) => {
//             let p = document.createElement('p');
//             p.innerHTML = parsedJson[i]['name'];
//             document.getElementById('here').appendChild(p);
//         });
//     });
// });

function setName (arr) {
    arr.forEach((el) => {
        document.querySelectorAll('.' + el).forEach((e, i) => {
            e.setAttribute('name', el + i);
        });
    });
}

function removeForm () {
    document.querySelectorAll('.btn-remove').forEach((e) => {
        e.addEventListener('click', function removeParent() {
            this.parentElement.remove();
            setName(['edu-year', 'school']);
            setName(['pos-year', 'desc']);
        });
    });
}

document.getElementById('btn-add-education').addEventListener('click', (e) => {
    e.preventDefault();
    let counter = document.querySelectorAll('.edu-year').length;
    if (counter < 9) {
        let eduForm = document.createElement('div');
        eduForm.innerHTML = `
            <label>Year:
                <input type="text" class="edu-year form-control" value="${faker.random.number({min:2000, max:2020})}">
            </label> <button class="btn-remove btn btn-danger">X</button><br><br>
            <label>School:
                <input type="text" class="school form-control">
            </label><br><br>`;
        document.getElementById('add-education').appendChild(eduForm);
        removeForm();
        setName(['edu-year', 'school']);
        $('.school').autocomplete({ source: "institutions.php" });
    }
});

document.getElementById('btn-add-position').addEventListener('click', (e) => {
    e.preventDefault();
    let counter = document.querySelectorAll('.pos-year').length;
    if (counter < 9) {
        let posForm = document.createElement('div');
        posForm.innerHTML = `
            <label>Year:
                <input type="text" class="pos-year form-control" value="${faker.random.number({min:2000, max:2020})}">
            </label> <button class="btn-remove btn btn-danger">X</button><br><br>
            <textarea class="desc form-control">${faker.name.jobTitle()}</textarea><br><br>`;
        document.getElementById('add-position').appendChild(posForm);
        removeForm();
        setName(['pos-year', 'desc']);
    }
});