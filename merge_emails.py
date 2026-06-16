import pandas as pd

base_path = 'database/seeders/dados de seed/base de alunos.csv'
rel_path = 'database/seeders/dados de seed/Relatorio.csv'

base = pd.read_csv(base_path)
rel = pd.read_csv(rel_path)

# Convert matricula to string to ensure exact matching
base['Matrícula'] = base['Matrícula'].astype(str).str.strip()
rel['Matrícula'] = rel['Matrícula'].astype(str).str.strip()

# Create mapping dictionary for emails
academic_emails = rel.set_index('Matrícula')['E-mail Acadêmico'].to_dict()
personal_emails = rel.set_index('Matrícula')['E-mail Pessoal'].to_dict()

# Map the emails back to base
base['E-mail Acadêmico'] = base['Matrícula'].map(academic_emails)
base['E-mail Pessoal'] = base['Matrícula'].map(personal_emails)

# Save back
base.to_csv(base_path, index=False)
print("Emails combinados com sucesso.")
