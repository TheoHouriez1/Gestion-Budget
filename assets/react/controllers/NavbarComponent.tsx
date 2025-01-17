import {
    Navbar,
    NavbarBrand,
    NavbarCollapse,
    NavbarCollapseBtn,
    NavbarContainer,
    NavbarItem,
    NavbarList,
  } from 'keep-react'
  
  export const NavbarComponent = () => {
    return (
      <Navbar>
        <NavbarContainer>
          <NavbarBrand>
            <img src={KeepLogo} alt="keep"/>
          </NavbarBrand>
          <NavbarList>
            <NavbarItem>Figma</NavbarItem>
            <NavbarItem>Documentation</NavbarItem>
            <NavbarItem>Blog</NavbarItem>
            <NavbarItem active>Get Started</NavbarItem>
          </NavbarList>
          <NavbarCollapseBtn />
          <NavbarCollapse>
            <NavbarItem>Figma</NavbarItem>
            <NavbarItem>Documentation</NavbarItem>
            <NavbarItem>Blog</NavbarItem>
            <NavbarItem active>Get Started</NavbarItem>
          </NavbarCollapse>
        </NavbarContainer>
      </Navbar>
    )
  }
  