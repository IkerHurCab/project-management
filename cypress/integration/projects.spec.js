describe('Login and Projects List View', () => {
    beforeEach(() => {
      // Realiza el login antes de visitar la página de proyectos
      cy.visit('/login');  // Visitamos la página de login
  
      // Ingresamos las credenciales
      cy.get('input[type="email"]').type('test@example.com');  // Reemplaza con un email de prueba
      cy.get('input[type="password"]').type('11111111');  // Reemplaza con una contraseña de prueba
  
      // Marca el checkbox "remember"
      cy.get('input[type="checkbox"]').check({ force: true });
  
      // Enviamos el formulario de login
      cy.get('form').submit();
     

   
   
    });
  
    describe('Projects List View', () => {
        beforeEach(() => {
      
            cy.visit('/projects');
          });
      
       
      it('displays the projects list', () => {
      
        cy.get('h1').should('contain', 'All Projects');
        cy.get('table').should('exist');
      });
  
      it('allows searching for projects', () => {
        cy.get('input[placeholder="Search projects..."]').type('Test Project');
        cy.get('table tbody tr').should('have.length.gt', 0);
      });
  
      it('opens the filter modal', () => {
        cy.contains('button', 'Filter').click();
        cy.get('.filter-modal').should('be.visible');
      });
  
      it('applies filters', () => {
        cy.contains('button', 'Filter').click();
        cy.get('.filter-modal').within(() => {
          cy.get('input[placeholder="Start Date"]').type('2023-01-01');
          cy.get('input[placeholder="End Date"]').type('2023-12-31');
          cy.get('select').select('In Progress');
          cy.contains('button', 'Apply Filters').click();
        });
        cy.get('.filter-tag').should('have.length.gt', 0);
      });
  
      it('navigates to a single project view', () => {
        cy.get('table tbody tr').first().click();
        cy.url().should('include', '/projects/');
      });
    });
  
    describe('Single Project View', () => {
      beforeEach(() => {
      
        cy.visit('/projects/9');
      });
  
      it('displays project details', () => {
        cy.get('h1').should('contain', 'Project Name');
        cy.get('.status-badge').should('exist');
      });
  
      it('switches between tabs', () => {
        cy.contains('button', 'Tasks').click();
        cy.get('.task-list').should('be.visible');
  
        cy.contains('button', 'Overview').click();
        cy.get('.project-details').should('be.visible');
      });
  
      it('displays the task list', () => {
        cy.contains('button', 'Tasks').click();
        cy.get('.task-list').should('have.length', 4);  // Assuming 4 status columns
      });
  
      it('allows dragging tasks between columns', () => {
        cy.contains('button', 'Tasks').click();
        const dataTransfer = new DataTransfer();
  
        cy.get('[data-status="to_do"] .task-card').first()
          .trigger('dragstart', { dataTransfer });
        cy.get('[data-status="in_progress"]')
          .trigger('drop', { dataTransfer });
  
        cy.get('[data-status="in_progress"] .task-card').should('have.length.gt', 0);
      });
  
      it('displays project progress', () => {
        cy.get('.progress-bar').should('exist');
        cy.get('.progress-text').should('contain', 'tasks completed');
      });
  
      it('shows team members', () => {
        cy.get('.team-members').should('exist');
        cy.get('.team-member').should('have.length.gt', 0);
      });
  
      it('displays priority tasks', () => {
        cy.get('.priority-tasks').should('exist');
        cy.get('.priority-task').should('have.length.lte', 3);
      });
  
      it('shows recent activity', () => {
        cy.get('.recent-activity').should('exist');
        cy.get('.activity-item').should('have.length.gt', 0);
      });
    });
  });
  