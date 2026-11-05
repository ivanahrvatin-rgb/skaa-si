/* SKAA faculty – one object per speaker. Used by faculty.html (the grid, split into the two teams) and speaker.html (each speaker's own subpage).
   name    – full name
   team    – 'ortho' (Orthopaedics team) or 'physio' (Physiotherapy team)
   captain – true for the team captain (shown first in the team)
   role    – line under the name (currently the role in the SKAA 2027 programme; replace with profession · institution, country)
   photo   – path to an image (e.g. assets/faculty/name.jpg) or '' to show initials
   cv      – short CV (5–8 sentences); a blank line in the text starts a new paragraph
   slug    – optional: the subpage address is speaker.html#slug; when omitted it is made from the name */
window.SKAA_FACULTY = [
  {name:"Benjamin Marjanovič", team:"ortho", captain:true, role:"Team captain · Expert jury", photo:"assets/faculty/benjamin-marjanovic.jpg", cv:"Biography to follow."},
  {name:"Bogdan Ambrožič", team:"ortho", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Emmanouil Antonogiannakis", team:"ortho", role:"Battle · Speaker", photo:"", cv:"Biography and photo to follow."},
  {name:"Markas Fiodorovas", team:"ortho", role:"Battle · Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Boštjan Gosnik", team:"ortho", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Neil Jain", team:"ortho", role:"Battle · Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Ladislav Kovačič", team:"ortho", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Guido Marra", team:"ortho", role:"Battle · Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"David Martinčič", team:"ortho", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Àngel Masferrer", team:"ortho", role:"Battle · Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Jakob Merkač", team:"ortho", role:"Speaker · Expert jury", photo:"assets/faculty/jakob-merkac.jpg", cv:"Biography to follow."},
  {name:"Nino Mirnik", team:"ortho", role:"Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Marko Nabergoj", team:"ortho", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Samo Novak", team:"ortho", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Boris Poberaj", team:"ortho", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Giuseppe Porcellini", team:"ortho", role:"Battle · Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Ivana Hrvatin", team:"physio", captain:true, role:"Physiotherapist, MSc · Valdoltra Orthopaedic Hospital, Slovenia", photo:"assets/faculty/ivana-hrvatin.jpg", cv:"Ivana Hrvatin graduated in March 2017 from the Faculty of Health Sciences, University of Ljubljana, and after graduation began working as a physiotherapist at the Gynaecological Clinic of the University Medical Centre Ljubljana. In the same year she enrolled in the master’s programme in physiotherapy at the Faculty of Health Sciences, University of Ljubljana, earning her Master’s degree in April 2021.\n\nSince July 2020 she has been employed at the Valdoltra Orthopaedic Hospital, where her clinical work focuses on musculoskeletal rehabilitation – particularly shoulder and knee joint function, muscle inhibition and pain management. Her research is primarily focused on shoulder and knee rehabilitation. She has co-authored several systematic reviews and research articles on topics including scapular stabilisation in subacromial syndrome, the effectiveness of graded activity for chronic low back pain, and the measurement properties of pain assessment tools.\n\nShe is also active in academic teaching: she is a lecturer at the Faculty of Health Sciences, University of Ljubljana, and has previously worked at the Faculty of Health Sciences, University of Primorska."},
  {name:"Matej Bombač", team:"physio", role:"Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Amy Bowzaylo", team:"physio", role:"Battle · Expert jury", photo:"assets/faculty/amy-bowzaylo.jpg", cv:"Biography and photo to follow."},
  {name:"Fabrizio Brindisino", team:"physio", role:"Physiotherapist, MSc, PhD candidate · University of Molise, Italy", photo:"assets/faculty/fabrizio-brindisino.jpg", cv:"Fabrizio Brindisino is a physiotherapist (MSc, OMPT-IFOMPT) and lecturer in physiotherapy at the Department of Medicine and Health Sciences “Vincenzo Tiberio”, University of Molise, Campobasso, Italy. He graduated from the University of Bari with 110/110 cum laude in November 2005 and immediately began working in Lecce in one of the largest centres of prosthetic implantology, where pre- and post-surgical rehabilitation was provided. He has attended, as a student and as a teacher, numerous courses, conferences and training seminars focused on surgical methods and on the evaluation and treatment of the aftermath of orthopaedic surgery and traumatology of the lower and upper limbs.\n\nConsidering advanced training in manual therapy and the rehabilitation of musculoskeletal disorders complementary and necessary to his activity, he attended the Master in Manual Therapy at the University of Padua in 2012.\n\nIn 2015 he was appointed by the University of Rome Tor Vergata to teach “Manual Therapy Applied to the Shoulder Girdle” in the post-graduate specialisation course in Manual Therapy Applied to Physiotherapy. In 2019 he received a contract teaching position from the University of Molise at the Master in Science and Practice in Musculoskeletal Physiotherapy, and in the same year he completed the two-year course at the University of Florence, obtaining a Master’s Degree in Rehabilitation Sciences of the Health Professions. He is currently a contract teacher in the Bachelor Degree Course in Physiotherapy at the University of Molise and is enrolled in the PhD programme in Technologies and Innovation in Medicine at the same university.\n\nHis professional growth in the evaluation and treatment of musculoskeletal disorders is continuous and expanding, as is his academic and teaching activity in various training courses for physiotherapists. He is the author and co-author of numerous books and scientific publications in indexed journals and a member of the Italian Society of Shoulder and Elbow Surgery (SICSeG), serving on its Guidelines and Registers and Rehabilitation committees."},
  {name:"Leopoldo Buttinioni", team:"physio", role:"Battle · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Alessandro Compagnin", team:"physio", role:"Battle · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Katarina Cunder", team:"physio", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Eric Hamrin Senorski", team:"physio", role:"Battle · Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Lana Jović", team:"physio", role:"Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Julija Kralj", team:"physio", role:"Physiotherapist · Valdoltra Orthopaedic Hospital, Slovenia", photo:"assets/faculty/julija-kralj.jpg", cv:"Julija Kralj graduated in 2023 from the Faculty of Health Sciences, University of Primorska. During her studies she completed an Erasmus traineeship at the Physiko- und Rheumatherapie – Institut für Physikalische Medizin in St. Pölten, Austria.\n\nAfter graduation she began working as an intern at the Valdoltra Orthopaedic Hospital, where she has since been employed as a physiotherapist. Her work focuses on musculoskeletal rehabilitation, particularly of the shoulder and knee joints, as well as the treatment of idiopathic scoliosis in paediatric patients.\n\nIn 2023 she enrolled in the master’s programme at the Faculty of Health Sciences, University of Primorska, where she is currently studying."},
  {name:"Neža Majdič", team:"physio", role:"Speaker · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Robbie Ohashi", team:"physio", role:"Battle · Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Maria Papatzikou", team:"physio", role:"Physiotherapist · Dr Manos Brilakis Orthopaedic Clinic, Greece", photo:"assets/faculty/maria-papatzikou.jpg", cv:"Maria Papatzikou is a physiotherapist based in Greece. She earned her degree in physiotherapy from the School of Health Professions at the Technological and Professional Institute of Athens.\n\nShe has specialised training in Neurodevelopmental Treatment, with expertise in movement observation, analysis and retraining.\n\nFor the past eight years she has collaborated exclusively with Dr Manos Brilakis’s Orthopaedic Clinic, where she leads the rehabilitation programmes for post-operative patients, ensuring optimal recovery outcomes through evidence-based practice."},
  {name:"Marina Rojc", team:"physio", role:"Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Špela Škerjanc", team:"physio", role:"Expert jury", photo:"", cv:"Biography and photo to follow."},
  {name:"Renata Vauhnik", team:"physio", role:"Associate Professor of Physiotherapy, PhD · University of Ljubljana, Slovenia", photo:"assets/faculty/renata-vauhnik.jpg", cv:"Renata Vauhnik, PhD, is Associate Professor of Physiotherapy at the Department of Physiotherapy, Faculty of Health Sciences, University of Ljubljana.\n\nShe graduated in physiotherapy from the University of Ljubljana in 1998, obtained her MSc in Physiotherapy from the University of East London in 1999 and her PhD in Physiotherapy from King’s College London in 2010."},
  {name:"Matej Voglar", team:"physio", role:"Physiotherapist, PhD · Faculty of Health Sciences, University of Primorska, Slovenia", photo:"assets/faculty/matej-voglar.jpg", cv:"Matej Voglar, PhD, Assistant Professor, holds a master’s degree in kinesiology and a bachelor’s degree in physiotherapy. He is Head of the Department of Physiotherapy and a lecturer at the Faculty of Health Sciences, University of Primorska.\n\nHis research focuses on the mechanisms underlying low back pain and on the effects of manual techniques. He holds an international licence in manual therapy and orthopaedic medicine from the Orthopaedic Medicine association, has completed training in the Maitland concept and numerous other courses in the management of musculoskeletal disorders."}
];

/* Helpers shared by both pages (no need to edit). */
window.SKAA_FACULTY_UTIL = (function(){
  function slugify(s){
    return String(s).normalize('NFD').replace(/[̀-ͯ]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'') || 'speaker';
  }
  var seen = {};
  window.SKAA_FACULTY.forEach(function(p){
    var base = p.slug ? slugify(p.slug) : slugify(p.name), s = base, n = 2;
    while (seen[s]) { s = base + '-' + n++; }
    seen[s] = true; p.slug = s;
  });
  function initials(n){ return String(n).split(/\s+/).map(function(w){ return w[0] || ''; }).join('').slice(0,2).toUpperCase(); }
  function photoHtml(p){
    if (p.photo) { var i = document.createElement('img'); i.src = p.photo; i.alt = ''; return i.outerHTML; }
    return initials(p.name).replace(/[&<>]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;'}[c]; });
  }
  function find(slug){ for (var i = 0; i < window.SKAA_FACULTY.length; i++) { if (window.SKAA_FACULTY[i].slug === slug) return i; } return -1; }
  var TEAMS = [{key:'ortho', label:'Orthopaedics team'}, {key:'physio', label:'Physiotherapy team'}];
  function teamLabel(key){ for (var i = 0; i < TEAMS.length; i++) { if (TEAMS[i].key === key) return TEAMS[i].label; } return ''; }
  /* The two teams in display order; within a team the captain comes first, then the order of the list above. */
  function teams(){
    return TEAMS.map(function(t){
      var people = window.SKAA_FACULTY.filter(function(p){ return p.team === t.key; });
      people.sort(function(a, b){ return (b.captain ? 1 : 0) - (a.captain ? 1 : 0); });
      return { key: t.key, label: t.label, people: people };
    }).filter(function(t){ return t.people.length; });
  }
  return { initials: initials, photoHtml: photoHtml, find: find, teams: teams, teamLabel: teamLabel };
})();
